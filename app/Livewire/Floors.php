<?php

namespace App\Livewire;

use App\Models\Floor;
use App\Models\Location;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

class Floors extends Component
{
    #[Locked]
    public $floor_id;

    #[Validate('required|exists:locations,id')]
    public $location_id;

    #[Validate('required|min:2|max:100')]
    public $name;

    #[Validate('nullable|integer|min:0')]
    public $sort_order = 0;

    #[Validate('boolean')]
    public $is_active = true;

    public function render()
    {
        return view('restaurante.floors.index', [
            'locations' => Location::active()->orderBy('name')->get()
        ]);
    }

    public function resetInput()
    {
        $this->reset([
            'location_id',
            'name',
            'sort_order',
            'is_active'
        ]);

        $this->sort_order = 0;
        $this->is_active = true;

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

        Floor::create([
            'location_id' => $this->location_id,
            'name' => $this->name,
            'sort_order' => $this->sort_order,
            'is_active' => (bool) $this->is_active,
        ]);

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $floor = Floor::findOrFail($id);

        $this->floor_id = $floor->id;
        $this->location_id = $floor->location_id;
        $this->name = $floor->name;
        $this->sort_order = $floor->sort_order;
        $this->is_active = (bool) $floor->is_active;

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        Floor::findOrFail($this->floor_id)->update([
            'location_id' => $this->location_id,
            'name' => $this->name,
            'sort_order' => $this->sort_order,
            'is_active' => (bool) $this->is_active,
        ]);

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
    }

    public function delete($id)
    {
        Floor::findOrFail($id)->delete();

        $this->dispatch('refreshDatatable');
    }
}
