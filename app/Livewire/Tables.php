<?php

namespace App\Livewire;

use App\Models\DiningTable;
use App\Models\Floor;
use App\Models\Location;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

class Tables extends Component
{
    #[Locked]
    public $table_id;

    #[Validate('required|exists:locations,id')]
    public $location_id;

    #[Validate('required|exists:floors,id')]
    public $floor_id;

    #[Validate('required|min:2|max:100')]
    public $name;

    #[Validate('required|integer|min:1|max:50')]
    public $capacity = 4;

    #[Validate('required')]
    public $status = DiningTable::AVAILABLE;

    #[Validate('boolean')]
    public $is_active = true;

    public function render()
    {
        return view('restaurante.tables.index', [
            'locations' => Location::active()
                ->orderBy('name')
                ->get(),

            'floors' => Floor::active()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function resetInput()
    {
        $this->reset([
            'location_id',
            'floor_id',
            'name',
            'capacity',
            'status',
            'is_active',
        ]);

        $this->capacity = 4;
        $this->status = DiningTable::AVAILABLE;
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

        DiningTable::create([
            'location_id' => $this->location_id,
            'floor_id' => $this->floor_id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'uuid' => Str::uuid(),
            'is_active' => (bool) $this->is_active,
        ]);

        $this->dispatch('store');
        $this->dispatch('refresh-table');
    }

    #[On('edit')]
    public function edit($id)
    {
        $table = DiningTable::findOrFail($id);

        $this->table_id = $table->id;
        $this->location_id = $table->location_id;
        $this->floor_id = $table->floor_id;
        $this->name = $table->name;
        $this->capacity = $table->capacity;
        $this->status = $table->status;
        $this->is_active = (bool) $table->is_active;

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        DiningTable::findOrFail($this->table_id)->update([
            'location_id' => $this->location_id,
            'floor_id' => $this->floor_id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'is_active' => (bool) $this->is_active,
        ]);

        $this->dispatch('update');
        $this->dispatch('refresh-table');
    }

    public function delete($id)
    {
        $table = DiningTable::findOrFail($id);

        if ($table->orders()->exists()) {

            $this->dispatch(
                'error',
                'No se puede eliminar la mesa porque tiene pedidos asociados.'
            );

            return;
        }

        $table->delete();

        $this->dispatch('refresh-table');
    }
}
