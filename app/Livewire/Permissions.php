<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Permissions extends Component
{
    #[Locked] 
    public $permission_id;

    #[Validate(['required', 'min:3', 'max:50'], as: 'Nombre del Permiso')]
    public $name;

    #[Validate('required|max:255', as: 'Descripción')]
    public $description;

    public function mount()
    {
        if (!Auth::user()?->can('permissions.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function render()
    {
        return view('security.permissions.index');
    }


    #[On('refreshPermissions')]
    public function resetInput()
    {
        $this->reset(['permission_id', 'name', 'description']);
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
        $validated = $this->validate();

        DB::transaction(function () {
            $role = Permission::create([
                'name' => $this->name,
                'description' => $this->description,
                'guard_name' => 'sanctum'
            ]);
            
            $this->dispatch('refreshDatatable');
            $this->dispatch('store');
            $this->resetInput();
        });
    }

    #[On('edit')]
    public function edit($id)
    {
        $this->resetInput();

        $permission = Permission::findOrFail($id);
        $this->permission_id = $id;
        $this->name = $permission->name;
        $this->description = $permission->description;
        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $validated = $this->validate();

        DB::transaction(function () {
            $permission = Permission::findOrFail($this->permission_id);
            $permission->update([
                'name' => $this->name,
                'description' => $this->description,
                'guard_name' => 'sanctum'
            ]);
            
            $this->dispatch('refreshDatatable');
            $this->dispatch('update');
            $this->resetInput();
        });
    }

    /**
    * Write code on Method
    *
    * @return response()
    */
    public function delete($id)
    {
        try {
            $role = Permission::findOrFail($id);
            $role->delete();
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            dd($e);
        }
    }
}