<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

class InventoryMovements extends Component
{
    #[Validate('required|exists:inventory_items,id')]
    public $inventory_item_id;

    #[Validate('required|in:waste_out,employee_consumption_out,adjustment')]
    public $type = 'waste_out';

    /** sólo aplica cuando type = adjustment */
    public $direction = 'decrease';

    #[Validate('required|numeric|min:0.0001')]
    public $quantity;

    public $unit_cost;

    #[Validate('required|min:5')]
    public $reason_note;

    public $items = [];
    public $types = [];

    public function mount()
    {
        if (!Auth::user()?->can('inventory_movements.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $this->items = InventoryItem::active()->with('baseUnit')->orderBy('name')->get();

        $this->types = [
            InventoryMovement::TYPE_WASTE_OUT => InventoryMovement::$types[InventoryMovement::TYPE_WASTE_OUT],
            InventoryMovement::TYPE_EMPLOYEE_CONSUMPTION_OUT => InventoryMovement::$types[InventoryMovement::TYPE_EMPLOYEE_CONSUMPTION_OUT],
            InventoryMovement::TYPE_ADJUSTMENT => InventoryMovement::$types[InventoryMovement::TYPE_ADJUSTMENT],
        ];
    }

    public function render()
    {
        return view('inventory.movements.index');
    }

    public function resetInput()
    {
        $this->reset(['inventory_item_id', 'quantity', 'unit_cost', 'reason_note', 'direction']);
        $this->type = 'waste_out';
        $this->direction = 'decrease';
        $this->resetValidation();
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();
        $this->dispatch('open-create-modal');
    }

    public function updatedInventoryItemId($value)
    {
        $item = collect($this->items)->firstWhere('id', (int) $value);
        $this->unit_cost = $item?->average_cost;
    }

    public function isIncrease(): bool
    {
        return $this->type === 'adjustment' && $this->direction === 'increase';
    }

    public function store()
    {
        $rules = [
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'type' => 'required|in:waste_out,employee_consumption_out,adjustment',
            'quantity' => 'required|numeric|min:0.0001',
            'reason_note' => 'required|min:5',
        ];

        if ($this->isIncrease()) {
            $rules['unit_cost'] = 'required|numeric|min:0';
        }

        $this->validate($rules);

        try {
            DB::transaction(function () {
                $item = InventoryItem::whereKey($this->inventory_item_id)->lockForUpdate()->first();

                $signedQuantity = $this->isIncrease() ? (float) $this->quantity : -(float) $this->quantity;

                if ($signedQuantity < 0 && ($item->stock + $signedQuantity) < 0) {
                    throw new \RuntimeException("No hay suficiente stock de \"{$item->name}\" para registrar esta salida.");
                }

                $unitCost = $this->isIncrease() ? (float) $this->unit_cost : (float) $item->average_cost;
                $newAverage = $signedQuantity > 0
                    ? $item->applyWeightedAverage($signedQuantity, $unitCost)
                    : $item->average_cost;

                $newStock = $item->stock + $signedQuantity;

                $item->update([
                    'stock' => $newStock,
                    'average_cost' => $newAverage,
                ]);

                InventoryMovement::create([
                    'inventory_item_id' => $item->id,
                    'type' => $this->type,
                    'quantity' => $signedQuantity,
                    'unit_cost' => $unitCost,
                    'balance_after' => $newStock,
                    'reason_note' => $this->reason_note,
                    'created_by' => Auth::id(),
                ]);
            });
        } catch (\RuntimeException $e) {
            $this->dispatch('error', $e->getMessage());
            return;
        }

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }
}
