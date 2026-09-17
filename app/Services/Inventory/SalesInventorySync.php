<?php

namespace App\Services\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\InventorySyncIssue;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Descuenta/repone inventario físico al vender o revertir productos en el
 * POS. Regla de negocio no negociable: nunca debe frenar una venta — todo
 * error interno se registra en el log y se ignora silenciosamente.
 */
class SalesInventorySync
{
    /**
     * Ajusta el stock del producto de inventario vinculado a $detail->product.
     * $delta positivo = unidades vendidas (resta stock); negativo = unidades
     * devueltas al pedido (repone stock).
     */
    public function adjustProductStock(OrderDetail $detail, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        try {
            $product = $detail->product;

            if (!$product || !$product->inventory_item_id) {
                if ($delta > 0) {
                    $this->recordSyncIssue($detail);
                }
                return;
            }

            DB::transaction(function () use ($product, $detail, $delta) {
                $item = InventoryItem::whereKey($product->inventory_item_id)->lockForUpdate()->first();

                if (!$item) {
                    return;
                }

                $signedQuantity = -$delta;
                $newStock = $item->stock + $signedQuantity;

                $item->update(['stock' => $newStock]);

                InventoryMovement::create([
                    'inventory_item_id' => $item->id,
                    'type' => $delta > 0
                        ? InventoryMovement::TYPE_SALE_OUT
                        : InventoryMovement::TYPE_SALE_REVERSAL_IN,
                    'quantity' => $signedQuantity,
                    'unit_cost' => $item->average_cost,
                    'balance_after' => $newStock,
                    'reference_id' => $detail->id,
                    'reference_type' => OrderDetail::class,
                    'created_by' => Auth::id(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('SalesInventorySync::adjustProductStock falló', [
                'order_detail_id' => $detail->id,
                'delta' => $delta,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sincroniza el descuento de empaque de una línea de mesa/vitrina contra
     * la cantidad marcada manualmente (EMPAQUE=N), sin doble conteo: aplica
     * sólo la diferencia entre lo ya descontado y $newPackagingQty.
     */
    public function syncLinePackaging(OrderDetail $detail, int $newPackagingQty): void
    {
        try {
            $packagingItems = InventoryItem::defaultPackaging()->get();

            if ($packagingItems->isEmpty()) {
                return;
            }

            DB::transaction(function () use ($packagingItems, $detail, $newPackagingQty) {
                foreach ($packagingItems as $packagingItem) {
                    $this->applyDiffForReference(
                        $packagingItem->id,
                        OrderDetail::class,
                        $detail->id,
                        -$newPackagingQty
                    );
                }
            });
        } catch (\Throwable $e) {
            Log::error('SalesInventorySync::syncLinePackaging falló', [
                'order_detail_id' => $detail->id,
                'new_packaging_qty' => $newPackagingQty,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Descuenta una sola vez el empaque de todo un pedido a domicilio
     * (icopor/bolsa por cada unidad vendida), referenciado al pedido.
     */
    public function applyDomicilioPackaging(Order $order): void
    {
        try {
            $totalQty = (int) $order->details->sum('quantity');

            if ($totalQty <= 0) {
                return;
            }

            $packagingItems = InventoryItem::defaultPackaging()->get();

            if ($packagingItems->isEmpty()) {
                return;
            }

            DB::transaction(function () use ($packagingItems, $order, $totalQty) {
                foreach ($packagingItems as $packagingItem) {
                    $this->applyDiffForReference(
                        $packagingItem->id,
                        Order::class,
                        $order->id,
                        -$totalQty
                    );
                }
            });
        } catch (\Throwable $e) {
            Log::error('SalesInventorySync::applyDomicilioPackaging falló', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Revierte por completo (a neto cero) todos los movimientos de venta
     * registrados contra una referencia (OrderDetail u Order). Idempotente:
     * si se llama dos veces, la segunda no encuentra nada que revertir.
     */
    public function reverseAllForReference(string $referenceType, int $referenceId): void
    {
        try {
            DB::transaction(function () use ($referenceType, $referenceId) {
                $itemIds = InventoryMovement::where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId)
                    ->distinct()
                    ->pluck('inventory_item_id');

                foreach ($itemIds as $itemId) {
                    $this->applyDiffForReference($itemId, $referenceType, $referenceId, 0);
                }
            });
        } catch (\Throwable $e) {
            Log::error('SalesInventorySync::reverseAllForReference falló', [
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Conveniencia: revierte todo el inventario descontado por un pedido
     * completo (cada línea + el empaque global de domicilio si aplica).
     */
    public function reverseAllForOrder(Order $order): void
    {
        foreach ($order->details as $detail) {
            $this->reverseAllForReference(OrderDetail::class, $detail->id);
        }

        $this->reverseAllForReference(Order::class, $order->id);
    }

    /**
     * Deja el neto de movimientos de un ítem contra una referencia en
     * exactamente $desiredSignedQuantity, creando sólo la diferencia.
     */
    private function applyDiffForReference(int $itemId, string $referenceType, int $referenceId, float $desiredSignedQuantity): void
    {
        $alreadyApplied = (float) InventoryMovement::where('inventory_item_id', $itemId)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->whereIn('type', [InventoryMovement::TYPE_SALE_OUT, InventoryMovement::TYPE_SALE_REVERSAL_IN])
            ->sum('quantity');

        $diff = $desiredSignedQuantity - $alreadyApplied;

        if (abs($diff) < 0.0000001) {
            return;
        }

        $item = InventoryItem::whereKey($itemId)->lockForUpdate()->first();

        if (!$item) {
            return;
        }

        $newStock = $item->stock + $diff;

        $item->update(['stock' => $newStock]);

        InventoryMovement::create([
            'inventory_item_id' => $item->id,
            'type' => $diff < 0
                ? InventoryMovement::TYPE_SALE_OUT
                : InventoryMovement::TYPE_SALE_REVERSAL_IN,
            'quantity' => $diff,
            'unit_cost' => $item->average_cost,
            'balance_after' => $newStock,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'created_by' => Auth::id(),
        ]);
    }

    private function recordSyncIssue(OrderDetail $detail): void
    {
        $issue = InventorySyncIssue::where('product_id', $detail->product_id)
            ->pending()
            ->first();

        if ($issue) {
            $issue->increment('occurrences');
            $issue->update([
                'last_order_detail_id' => $detail->id,
                'last_seen_at' => now(),
            ]);

            return;
        }

        InventorySyncIssue::create([
            'product_id' => $detail->product_id,
            'reason' => InventorySyncIssue::REASON_UNLINKED_PRODUCT,
            'occurrences' => 1,
            'last_order_detail_id' => $detail->id,
            'last_seen_at' => now(),
        ]);
    }
}
