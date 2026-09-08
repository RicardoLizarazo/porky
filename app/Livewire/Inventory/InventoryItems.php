<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\InventoryItem;
use App\Models\InventoryItemUnit;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class InventoryItems extends Component
{
    #[Locked]
    public $inventory_item_id;

    #[Validate('required|min:2|max:255')]
    public $name;

    #[Validate('nullable|max:100')]
    public $code;

    #[Validate('required|in:raw_material,resale,packaging,cleaning')]
    public $type = 'raw_material';

    #[Validate('required|exists:units,id')]
    public $base_unit_id;

    #[Validate('nullable|numeric|min:0')]
    public $min_stock;

    #[Validate('boolean')]
    public $is_active = true;

    #[Validate('nullable')]
    public $notes;

    /** unidades alternas de compra/manejo: [['unit_id'=>, 'factor_to_base'=>], ...] */
    public $altUnits = [];

    public $units = [];
    public $types = [];

    public function mount()
    {
        if (!Auth::user()?->can('inventory_items.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $this->units = Unit::orderBy('name')->get();
        $this->types = InventoryItem::$types;
    }

    public function render()
    {
        return view('inventory.inventory-items.index', [
            'totalValue' => InventoryItem::query()
                ->selectRaw('SUM(stock * average_cost) as total')
                ->value('total') ?? 0,
        ]);
    }

    public function resetInput()
    {
        $this->reset([
            'name', 'code', 'type', 'base_unit_id', 'min_stock',
            'is_active', 'notes', 'altUnits',
        ]);

        $this->type = 'raw_material';
        $this->is_active = true;
        $this->resetValidation();
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();
        $this->dispatch('open-create-modal');
    }

    public function addAltUnit()
    {
        $this->altUnits[] = ['unit_id' => '', 'factor_to_base' => ''];
    }

    public function removeAltUnit($index)
    {
        unset($this->altUnits[$index]);
        $this->altUnits = array_values($this->altUnits);
    }

    private function syncAltUnits(InventoryItem $item)
    {
        $item->itemUnits()->delete();

        foreach ($this->altUnits as $row) {
            if (empty($row['unit_id']) || $row['factor_to_base'] === '' || $row['unit_id'] == $item->base_unit_id) {
                continue;
            }

            InventoryItemUnit::create([
                'inventory_item_id' => $item->id,
                'unit_id' => $row['unit_id'],
                'factor_to_base' => $row['factor_to_base'],
            ]);
        }
    }

    public function store()
    {
        $this->validate();

        $item = InventoryItem::create([
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'base_unit_id' => $this->base_unit_id,
            'min_stock' => $this->min_stock ?: null,
            'is_active' => (bool) $this->is_active,
            'notes' => $this->notes,
        ]);

        $this->syncAltUnits($item);

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $this->resetInput();

        $item = InventoryItem::with('itemUnits')->findOrFail($id);

        $this->inventory_item_id = $id;
        $this->name = $item->name;
        $this->code = $item->code;
        $this->type = $item->type;
        $this->base_unit_id = $item->base_unit_id;
        $this->min_stock = $item->min_stock;
        $this->is_active = (bool) $item->is_active;
        $this->notes = $item->notes;

        $this->altUnits = $item->itemUnits->map(fn($u) => [
            'unit_id' => $u->unit_id,
            'factor_to_base' => $u->factor_to_base,
        ])->toArray();

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        $item = InventoryItem::findOrFail($this->inventory_item_id);

        $item->update([
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'base_unit_id' => $this->base_unit_id,
            'min_stock' => $this->min_stock ?: null,
            'is_active' => (bool) $this->is_active,
            'notes' => $this->notes,
        ]);

        $this->syncAltUnits($item);

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
    }

    public function delete($id)
    {
        try {
            $item = InventoryItem::findOrFail($id);

            if ($item->stock > 0) {
                $this->dispatch('error', 'No se puede eliminar: el producto tiene existencias en inventario.');
                return;
            }

            $item->delete();
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            $this->dispatch('error', 'No se puede eliminar: el producto tiene movimientos o compras asociadas.');
        }
    }
}
