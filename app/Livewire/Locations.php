<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Location;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Locations extends Component
{
    #[Locked]
    public $location_id;

    #[Validate('required|min:3')]
    public $name;

    #[Validate('nullable')]
    public $description;

    #[Validate('required|boolean')]
    public $is_active = true;

    public $schedule = [];

    public function mount()
    {
        $this->initSchedule();
    }

    public function initSchedule()
    {
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

        foreach ($days as $day) {
            $this->schedule[$day] = [
                'active' => false,
                'start' => '08:00',
                'end' => '22:00',
            ];
        }
    }

    public function render()
    {
        return view('restaurante.locations.index');
    }

    public function resetInput()
    {
        $this->reset(['name','description','is_active','schedule']);
        $this->initSchedule();
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
        $this->validate([
            'name' => 'required|min:3',
        ]);

        Location::create([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'schedule' => $this->schedule,
        ]);

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    #[On('edit')]
    public function edit($id)
    {
        $location = Location::findOrFail($id);

        $this->location_id = $id;
        $this->name = $location->name;
        $this->description = $location->description;
        $this->is_active = $location->is_active;

        $this->schedule = $location->schedule ?? [];
        if (empty($this->schedule)) {
            $this->initSchedule();
        }

        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
        ]);

        Location::findOrFail($this->location_id)->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'schedule' => $this->schedule,
        ]);

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
    }

    public function delete($id)
    {
        Location::findOrFail($id)->delete();
        $this->dispatch('refreshDatatable');
    }
}
