<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Units extends Component
{
    #[Locked]
    public $unit_id;

    #[Validate('required|min:2|max:100')]
    public $name;

    #[Validate('required|max:20')]
    public $abbreviation;

    public function mount()
    {
        if (!Auth::user()?->can('units.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function render()
    {
        return view('inventory.units.index');
    }

    public function resetInput()
    {
        $this->reset(['name', 'abbreviation']);
        $this->resetValidation();
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();
        $this->dispatch('open-create-modal');
    }

    public function store()
    {
        $this->validate();

        Unit::create([
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
        ]);

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);

        $this->unit_id = $id;
        $this->name = $unit->name;
        $this->abbreviation = $unit->abbreviation;

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        Unit::findOrFail($this->unit_id)->update([
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
        ]);

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
    }

    public function delete($id)
    {
        try {
            Unit::findOrFail($id)->delete();
            $this->dispatch('refreshDatatable');
        } catch (QueryException $e) {
            $this->dispatch('error', 'No se puede eliminar: la unidad está en uso por algún producto de inventario o compra.');
        }
    }
}
