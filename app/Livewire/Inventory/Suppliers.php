<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Suppliers extends Component
{
    #[Locked]
    public $supplier_id;

    #[Validate('required|min:3|max:255')]
    public $name;

    #[Validate('nullable|max:50')]
    public $nit;

    #[Validate('nullable|max:150')]
    public $contact_name;

    #[Validate('nullable|max:50')]
    public $phone;

    #[Validate('nullable|email|max:255')]
    public $email;

    #[Validate('nullable|max:255')]
    public $address;

    #[Validate('boolean')]
    public $status = true;

    #[Validate('nullable')]
    public $notes;

    public $availableItems = [];

    /** @var array<int,bool> inventory_item_id => selected */
    public $selectedItems = [];

    /** @var array<int,string> inventory_item_id => sku del proveedor */
    public $skus = [];

    public function mount()
    {
        if (!Auth::user()?->can('suppliers.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $this->availableItems = InventoryItem::active()->orderBy('name')->get();
    }

    public function render()
    {
        return view('inventory.suppliers.index');
    }

    public function resetInput()
    {
        $this->reset([
            'name', 'nit', 'contact_name', 'phone', 'email', 'address',
            'status', 'notes', 'selectedItems', 'skus',
        ]);

        $this->status = true;
        $this->resetValidation();
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();
        $this->dispatch('open-create-modal');
    }

    private function syncItems(Supplier $supplier)
    {
        $syncData = [];

        foreach ($this->selectedItems as $itemId => $checked) {
            if ($checked) {
                $syncData[$itemId] = ['supplier_sku' => $this->skus[$itemId] ?? null];
            }
        }

        $supplier->inventoryItems()->sync($syncData);
    }

    public function store()
    {
        $this->validate();

        $supplier = Supplier::create([
            'name' => $this->name,
            'nit' => $this->nit,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'status' => (bool) $this->status,
            'notes' => $this->notes,
        ]);

        $this->syncItems($supplier);

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $this->resetInput();

        $supplier = Supplier::with('inventoryItems')->findOrFail($id);

        $this->supplier_id = $id;
        $this->name = $supplier->name;
        $this->nit = $supplier->nit;
        $this->contact_name = $supplier->contact_name;
        $this->phone = $supplier->phone;
        $this->email = $supplier->email;
        $this->address = $supplier->address;
        $this->status = (bool) $supplier->status;
        $this->notes = $supplier->notes;

        foreach ($supplier->inventoryItems as $item) {
            $this->selectedItems[$item->id] = true;
            $this->skus[$item->id] = $item->pivot->supplier_sku;
        }

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        $supplier = Supplier::findOrFail($this->supplier_id);

        $supplier->update([
            'name' => $this->name,
            'nit' => $this->nit,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'status' => (bool) $this->status,
            'notes' => $this->notes,
        ]);

        $this->syncItems($supplier);

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
    }

    public function delete($id)
    {
        try {
            Supplier::findOrFail($id)->delete();
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            $this->dispatch('error', 'No se puede eliminar: el proveedor tiene compras registradas.');
        }
    }
}
