<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Auth;

class Users extends Component
{
    #[Locked] 
    public $user_id;

    #[Validate('required', as: 'Nombre')]
    public $name;

    #[Validate('required|email|max:255|unique:users', as: 'Email')]
    public $email;

    #[Validate('nullable|min:8|confirmed', as: 'Contraseña')]
    public $password;

    #[Validate('nullable|same:password', as: 'Confirmacion de Contraseña')]
    public $password_confirmation;
    
    // Ahora soporta múltiples roles
    #[Validate('required|array|min:1', as: 'Roles')]
    public $roles = [];

    public function mount()
    {
        if (!Auth::user()?->can('users.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }
    
    public function render()
    {
        $allRoles = Role::all();
        return view('security.users.index', [
            'allRoles' => $allRoles,
        ]);
    }

    public function resetInput()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'roles']);
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'roles' => 'required|array|min:1',
        ]);

        try {
            $user = new User;
            $user->name = $this->name;
            $user->email = $this->email;
            $user->password = bcrypt($this->password);
            $user->save();

            // asignar múltiples roles
            $user->syncRoles(
                Role::whereIn('id', $this->roles)->pluck('name')->toArray()
            );

            $this->dispatch('store');
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    #[On('edit')]
    public function edit($id)
    {
        $this->resetInput();
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->roles = $user->roles->pluck('id')->toArray();
        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $rules = [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user_id,
            'roles' => 'required|array|min:1',
        ];

        if ($this->password) {
            $rules['password'] = [
                'required',
                'min:8',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ];
            $rules['password_confirmation'] = 'required|same:password';
        }

        $this->validate($rules);

        try {
            $user = User::findOrFail($this->user_id);

            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $updateData['password'] = Hash::make($this->password);
            }

            $user->update($updateData);

            // múltiples roles
            $user->syncRoles(
                Role::whereIn('id', $this->roles)->pluck('name')->toArray()
            );

            // limpiar cache de permisos
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $this->dispatch('update');
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function delete($id)
    {
        try {
            User::findOrFail($id)->delete();
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
