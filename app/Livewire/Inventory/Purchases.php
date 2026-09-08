<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Purchases extends Component
{
    #[Locked]
    public $purchase_id;

    #[Locked]
    public $status;

    #[Validate('required|exists:suppliers,id')]
    public $supplier_id;

    #[Validate('required|max:100')]
    public $invoice_number;

    #[Validate('required|date')]
    public $purchase_date;

    #[Validate('nullable')]
    public $notes;

    public $details = [];

    public $suppliers = [];
    public $supplierItems = [];

    /** true cuando el modal se abre sólo para consulta (compra confirmada) */
    public $readOnly = false;

    public function mount()
    {
        if (!Auth::user()?->can('purchases.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $this->suppliers = Supplier::active()->orderBy('name')->get();
        $this->purchase_date = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('inventory.purchases.index', [
            'formTotal' => collect($this->details)->sum(fn($d) =>
                (float) ($d['quantity'] ?? 0) * (float) ($d['unit_cost'] ?? 0)
            ),
        ]);
    }

    public function resetInput()
    {
        $this->reset([
            'purchase_id', 'status', 'supplier_id', 'invoice_number',
            'purchase_date', 'notes', 'details', 'supplierItems', 'readOnly',
        ]);

        $this->purchase_date = now()->format('Y-m-d');
        $this->resetValidation();
    }

    public function updatedSupplierId($value)
    {
        $this->loadSupplierItems($value);
        $this->details = [];

        if ($value) {
            $this->addDetail();
        }
    }

    private function loadSupplierItems($supplierId)
    {
        $this->supplierItems = $supplierId
            ? Supplier::find($supplierId)?->inventoryItems()->with('baseUnit', 'itemUnits.unit')->get() ?? []
            : [];
    }

    public function unitOptionsForItem($itemId)
    {
        $item = collect($this->supplierItems)->firstWhere('id', (int) $itemId);

        if (!$item) {
            return [];
        }

        $options = [
            ['id' => $item->base_unit_id, 'label' => $item->baseUnit->name . ' (' . $item->baseUnit->abbreviation . ')'],
        ];

        foreach ($item->itemUnits as $itemUnit) {
            $options[] = [
                'id' => $itemUnit->unit_id,
                'label' => $itemUnit->unit->name . ' (' . $itemUnit->unit->abbreviation . ')',
            ];
        }

        return $options;
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();
        $this->addDetail();
        $this->dispatch('open-create-modal');
    }

    public function addDetail()
    {
        $this->details[] = [
            'inventory_item_id' => '',
            'unit_id' => '',
            'quantity' => '',
            'unit_cost' => '',
        ];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    private function detailsValidationRules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|max:100',
            'purchase_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'details.*.unit_id' => 'required|exists:units,id',
            'details.*.quantity' => 'required|numeric|min:0.0001',
            'details.*.unit_cost' => 'required|numeric|min:0',
        ];
    }

    public function store()
    {
        $this->validate($this->detailsValidationRules());

        try {
            DB::transaction(function () {
                $purchase = Purchase::create([
                    'supplier_id' => $this->supplier_id,
                    'invoice_number' => $this->invoice_number,
                    'purchase_date' => $this->purchase_date,
                    'status' => Purchase::STATUS_DRAFT,
                    'notes' => $this->notes,
                    'created_by' => Auth::id(),
                    'total' => 0,
                ]);

                $this->saveDetails($purchase);
            });

            $this->dispatch('store');
            $this->dispatch('refreshDatatable');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->dispatch('error', 'Ya existe una compra registrada con ese número de factura para este proveedor.');
        }
    }

    private function saveDetails(Purchase $purchase)
    {
        $purchase->details()->delete();

        $total = 0;

        foreach ($this->details as $row) {
            $item = InventoryItem::findOrFail($row['inventory_item_id']);
            $factor = $item->factorToBase((int) $row['unit_id']);

            $quantity = (float) $row['quantity'];
            $unitCost = (float) $row['unit_cost'];
            $baseQuantity = $quantity * $factor;
            $subtotal = $quantity * $unitCost;

            $purchase->details()->create([
                'inventory_item_id' => $item->id,
                'unit_id' => $row['unit_id'],
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'base_quantity' => $baseQuantity,
                'subtotal' => $subtotal,
            ]);

            $total += $subtotal;
        }

        $purchase->update(['total' => $total]);
    }

    #[On('edit')]
    public function edit($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        if ($purchase->status !== Purchase::STATUS_DRAFT) {
            $this->show($id);
            return;
        }

        $this->resetInput();
        $this->fillFromPurchase($purchase);
        $this->readOnly = false;

        $this->dispatch('open-edit-modal');
    }

    #[On('view')]
    public function show($id)
    {
        $this->resetInput();

        $purchase = Purchase::with('details')->findOrFail($id);

        $this->fillFromPurchase($purchase);
        $this->readOnly = true;

        $this->dispatch('open-edit-modal');
    }

    private function fillFromPurchase(Purchase $purchase)
    {
        $this->purchase_id = $purchase->id;
        $this->status = $purchase->status;
        $this->supplier_id = $purchase->supplier_id;
        $this->invoice_number = $purchase->invoice_number;
        $this->purchase_date = $purchase->purchase_date->format('Y-m-d');
        $this->notes = $purchase->notes;

        $this->loadSupplierItems($purchase->supplier_id);

        $this->details = $purchase->details->map(fn($d) => [
            'inventory_item_id' => $d->inventory_item_id,
            'unit_id' => $d->unit_id,
            'quantity' => $d->quantity,
            'unit_cost' => $d->unit_cost,
        ])->toArray();
    }

    public function update()
    {
        $purchase = Purchase::findOrFail($this->purchase_id);

        if ($purchase->status !== Purchase::STATUS_DRAFT) {
            $this->dispatch('error', 'Esta compra ya fue confirmada y no se puede editar.');
            return;
        }

        $this->validate($this->detailsValidationRules());

        try {
            DB::transaction(function () use ($purchase) {
                $purchase->update([
                    'supplier_id' => $this->supplier_id,
                    'invoice_number' => $this->invoice_number,
                    'purchase_date' => $this->purchase_date,
                    'notes' => $this->notes,
                ]);

                $this->saveDetails($purchase);
            });

            $this->dispatch('update');
            $this->dispatch('refreshDatatable');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->dispatch('error', 'Ya existe una compra registrada con ese número de factura para este proveedor.');
        }
    }

    public function confirm($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        if ($purchase->status !== Purchase::STATUS_DRAFT) {
            $this->dispatch('error', 'Esta compra ya fue confirmada.');
            return;
        }

        if ($purchase->details->isEmpty()) {
            $this->dispatch('error', 'La compra no tiene líneas registradas.');
            return;
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->details as $detail) {
                $item = InventoryItem::whereKey($detail->inventory_item_id)->lockForUpdate()->first();

                $unitCostBase = $detail->base_quantity > 0
                    ? $detail->subtotal / $detail->base_quantity
                    : 0;

                $newAverage = $item->applyWeightedAverage($detail->base_quantity, $unitCostBase);
                $newStock = $item->stock + $detail->base_quantity;

                $item->update([
                    'stock' => $newStock,
                    'average_cost' => $newAverage,
                ]);

                InventoryMovement::create([
                    'inventory_item_id' => $item->id,
                    'type' => InventoryMovement::TYPE_PURCHASE_IN,
                    'quantity' => $detail->base_quantity,
                    'unit_cost' => $unitCostBase,
                    'balance_after' => $newStock,
                    'reference_id' => $purchase->id,
                    'reference_type' => Purchase::class,
                    'created_by' => Auth::id(),
                ]);

                DB::table('supplier_inventory_item')->updateOrInsert(
                    ['supplier_id' => $purchase->supplier_id, 'inventory_item_id' => $item->id],
                    ['last_price' => $unitCostBase, 'updated_at' => now()]
                );
            }

            $purchase->update([
                'status' => Purchase::STATUS_CONFIRMED,
                'confirmed_at' => now(),
                'confirmed_by' => Auth::id(),
            ]);
        });

        $this->dispatch('confirmed');
        $this->dispatch('refreshDatatable');
    }

    public function delete($id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status !== Purchase::STATUS_DRAFT) {
            $this->dispatch('error', 'Sólo se pueden eliminar compras en borrador.');
            return;
        }

        $purchase->delete();
        $this->dispatch('refreshDatatable');
    }
}
