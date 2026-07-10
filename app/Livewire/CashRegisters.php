<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Floor;
use App\Models\Location;
use App\Models\CashRegister;
use App\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

class CashRegisters extends Component
{
    #[Locked]
    public $cash_register_id;

    #[Validate('required')]
    public $location_id;

    #[Validate('required')]
    public $floor_id;

    #[Validate('required|min:3')]
    public $name;

    #[Validate('boolean')]
    public $is_active = true;

    #[Validate('nullable|exists:users,id')]
    public $responsible_user_id;

    public function render()
    {
        return view(
            'restaurante.cash-registers.index',
            [
                'locations' => Location::active()->get(),

                'floors' => Floor::active()
                    ->orderBy('name')
                    ->get(),

                'users' => User::permission('cashier.open')
                    ->orderBy('name')
                    ->get(),
            ]
        );
    }

    public function resetInput()
    {
        $this->reset([
            'location_id',
            'floor_id',
            'responsible_user_id',
            'name',
            'is_active'
        ]);

        $this->is_active = true;

        $this->resetValidation();
    }

    #[On('create')]
    public function create()
    {
        $this->resetInput();

        $this->dispatch(
            'open-create-modal'
        );
    }

    public function store()
    {
        $this->validate();

        CashRegister::create([
            'location_id'         => $this->location_id,
            'floor_id'            => $this->floor_id,
            'responsible_user_id' => $this->responsible_user_id,
            'name'                => $this->name,
            'is_active'           => (bool) $this->is_active,
        ]);

        $this->dispatch('store');

        $this->dispatch(
            'refreshDatatable'
        );
    }

    #[On('edit')]
    public function edit($id)
    {
        $cash = CashRegister::findOrFail($id);
        $this->cash_register_id = $cash->id;
        $this->location_id = $cash->location_id;
        $this->floor_id = $cash->floor_id;
        $this->responsible_user_id = $cash->responsible_user_id;
        $this->name = $cash->name;
        $this->is_active = $cash->is_active;

        $this->dispatch(
            'open-edit-modal'
        );
    }

    public function update()
    {
        $this->validate();

        CashRegister::findOrFail(
            $this->cash_register_id
        )->update([

            'location_id'         => $this->location_id,
            'floor_id'            => $this->floor_id,
            'responsible_user_id' => $this->responsible_user_id,
            'name'                => $this->name,
            'is_active'           => (bool) $this->is_active,

        ]);

        $this->dispatch('update');

        $this->dispatch(
            'refreshDatatable'
        );
    }

    public function delete($id)
    {
        CashRegister::findOrFail($id)
            ->delete();

        $this->dispatch(
            'refreshDatatable'
        );
    }
}