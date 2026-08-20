<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Floor;
use App\Models\DiningTable;
use App\Models\TableMerge;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FloorMap extends Component
{
    public $selectedFloor;

    public ?int $mergingTableId = null;

    public function mount()
    {
        $this->selectedFloor = Floor::active()
            ->orderBy('sort_order')
            ->value('id');
    }

    public function openTable($tableId)
    {
        $table = DiningTable::with('activeOrder')
            ->findOrFail($tableId);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR ACCESO
        |--------------------------------------------------------------------------
        */

        if (!$table->canBeAccessedBy(Auth::user())) {

            $this->dispatch(
                'show-error',
                message: 'La mesa está siendo atendida por '.$table->waiter?->name
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ORDEN EFECTIVA
        |--------------------------------------------------------------------------
        | Si la mesa es la principal o no está unida, effectiveOrder() devuelve
        | su propia activeOrder. Si es una mesa SECUNDARIA (unida a otra), esta
        | mesa no tiene activeOrder propia, así que se devuelve la orden de la
        | mesa principal. Solo si sigue en null es que realmente no hay orden.
        |--------------------------------------------------------------------------
        */

        $order = $table->effectiveOrder();

        /*
        |--------------------------------------------------------------------------
        | CREAR ORDEN SI REALMENTE NO EXISTE
        |--------------------------------------------------------------------------
        */

        if (!$order) {

            $order = Order::create([

                'customer_id' => null,

                'user_id' => Auth::id(),

                'location_id' => $table->location_id,
                'floor_id' => $table->floor_id,
                'table_id' => $table->id,

                'status_id' => Order::STATUS_PENDING,

                /*
                |--------------------------------------------------------------------------
                | TIPO RESTAURANTE
                |--------------------------------------------------------------------------
                */

                'type_id' => 3, // mesa/restaurante
            ]);

            $table->update([
                'waiter_id' => Auth::id(),
                'status' => DiningTable::OCCUPIED,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIONAR POS
        |--------------------------------------------------------------------------
        */

        return redirect()->route('pos.show', $order->id);
    }

    public function startMerge($tableId)
    {
        if (!Auth::user()->can('floor_map.merge_tables')) {
            return;
        }

        $table = DiningTable::with('activeOrder')->findOrFail($tableId);

        if (!$table->activeOrder) {
            $this->dispatch('show-error', title: 'Mesa vacía', message: 'Solo puedes iniciar la unión desde una mesa con orden activa.');
            return;
        }

        if (!$table->canBeAccessedBy(Auth::user())) {
            $this->dispatch('show-error', title: 'Mesa ocupada', message: 'No puedes unir una mesa que no es tuya.');
            return;
        }

        if ($table->mergeAsPrimary()->exists() || $table->mergeAsSecondary()->exists()) {
            $this->dispatch('show-error', title: 'Mesa ya unida', message: 'Esta mesa ya está unida a otra.');
            return;
        }

        $this->mergingTableId = $table->id;
    }

    public function cancelMerge()
    {
        $this->mergingTableId = null;
    }

    public function confirmMerge($targetTableId)
    {
        if (!$this->mergingTableId || $this->mergingTableId == $targetTableId) {
            return;
        }

        $primary = DiningTable::with('activeOrder')->findOrFail($this->mergingTableId);
        $target  = DiningTable::with('activeOrder')->findOrFail($targetTableId);

        if ($target->mergeAsPrimary()->exists() || $target->mergeAsSecondary()->exists()) {
            $this->dispatch('show-error', title: 'Mesa ya unida', message: 'La mesa destino ya está unida a otra.');
            $this->mergingTableId = null;
            return;
        }

        DB::transaction(function () use ($primary, $target) {

            $absorbedOrderId = null;

            if ($target->activeOrder) {
                /*
                |----------------------------------------------------------------
                | CASO B: la mesa destino ya tenía orden -> se mueven sus items
                | a la orden principal y se SUMAN sus totales, para que al cobrar
                | refleje ambas mesas. La orden absorbida queda como Fusionada
                | (se conserva su registro histórico, solo se excluye del cobro).
                |----------------------------------------------------------------
                */
                $absorbedOrder = $target->activeOrder;

                $absorbedOrder->details()->update(['order_id' => $primary->activeOrder->id]);
                $absorbedOrderId = $absorbedOrder->id;

                $primary->activeOrder->update([
                    'subtotal'        => $primary->activeOrder->subtotal + $absorbedOrder->subtotal,
                    'tax'             => $primary->activeOrder->tax + $absorbedOrder->tax,
                    'discount'        => $primary->activeOrder->discount + $absorbedOrder->discount,
                    'packaging_total' => $primary->activeOrder->packaging_total + $absorbedOrder->packaging_total,
                    'tip'             => $primary->activeOrder->tip + $absorbedOrder->tip,
                    'total'           => $primary->activeOrder->total + $absorbedOrder->total,
                    'total_items'     => $primary->activeOrder->total_items + $absorbedOrder->total_items,
                ]);

                $absorbedOrder->update(['status_id' => Order::STATUS_MERGED]);
            }
            // CASO A: la mesa destino estaba libre -> no hay orden que fusionar, solo se anexa.

            TableMerge::create([
                'primary_table_id'   => $primary->id,
                'secondary_table_id' => $target->id,
                'primary_order_id'   => $primary->activeOrder->id,
                'absorbed_order_id'  => $absorbedOrderId,
                'created_by'         => Auth::id(),
            ]);

            $target->update([
                'status'    => DiningTable::OCCUPIED,
                'waiter_id' => $primary->waiter_id,
            ]);
        });

        $this->mergingTableId = null;
        $this->dispatch('show-success', message: 'Mesas unidas correctamente.');
    }

    public function render()
    {
        return view('restaurante.floor-map.index', [

            'floors' => Floor::active()
                ->orderBy('sort_order')
                ->get(),

            'tables' => DiningTable::with([
                    'floor',
                    'activeOrder.customer',
                    'waiter',
                    'mergeAsPrimary.secondaryTable',
                    'mergeAsSecondary.primaryTable',
                ])
                ->where('floor_id', $this->selectedFloor)
                ->where('is_active', true)
                ->get(),
        ]);
    }
}