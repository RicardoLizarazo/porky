<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Roles extends Component
{
    #[Locked] 
    public $role_id;

    #[Validate(['required', 'min:3', 'max:50'], as: 'Nombre del Rol')]
    public $name = '';

    #[Validate('required|max:255', as: 'Descripción')]
    public $description;

    public $selectedPermissions = [];

    public $permissions = [];

    public function mount()
    {
        if (!Auth::user()?->can('roles.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $this->loadPermissions();
    }

    protected function loadPermissions()
    {
        $this->permissions = Permission::orderBy('name')->get();
    }

    /**
     * ✅ Propiedad computada para agrupar permisos por módulo en español
     */
    public function getGroupedPermissionsProperty()
    {
        $modules = [
            'employees'         => 'Empleados',
            'users'             => 'Usuarios',
            'roles'             => 'Roles',
            'permissions'       => 'Permisos',
            'approval_profiles' => 'Perfiles de aprobación',
            'audit_logs'        => 'Registros de auditoría',
            'consultations'     => 'Consultas',
            'dashboard'         => 'Panel',
            'settings'          => 'Configuración',
            'reports'           => 'Reportes',
            'system'            => 'Sistema',
            'contacts'          => 'Contactos',
            'specialists'       => 'Especialistas',
            'accounting'        => 'Contabilidad',
        ];

        return collect($this->permissions)->groupBy(function ($permission) use ($modules) {
            $name = $permission->name ?? '';
            $parts = explode('.', $name);
            $mod = $parts[0] ?? 'general';

            return $modules[$mod] ?? Str::headline($mod);
        })->sortKeys(); // Ordenar alfabéticamente por módulo
    }

    public function render()
    {
        return view('security.roles.index');
    }

    #[On('refreshRoles')]
    public function resetInput()
    {
        $this->reset(['role_id', 'name', 'description', 'selectedPermissions']);
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
            $role = Role::create([
                'name' => $this->name,
                'description' => $this->description,
                'guard_name' => 'sanctum'
            ]);
            
            if (!empty($this->selectedPermissions)) {
                $permissions = Permission::whereIn('id', $this->selectedPermissions)
                    ->where('guard_name', 'sanctum')
                    ->get();
                
                $role->syncPermissions($permissions);
            }
            
            $this->dispatch('refreshDatatable');
            $this->dispatch('store');
            $this->resetInput();
        });
    }

    #[On('edit')]
    public function edit($id)
    {
        $this->resetInput();
        $role = Role::with('permissions')->findOrFail($id);
        
        $this->role_id = $id;
        $this->name = $role->name;
        $this->description = $role->description;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $validated = $this->validate();

        DB::transaction(function () {
            $role = Role::findOrFail($this->role_id);

            $role->update([
                'name'        => $this->name,
                'description' => $this->description,
                'guard_name'  => 'sanctum',
            ]);

            $permissions = Permission::whereIn('id', $this->selectedPermissions ?? [])
                ->pluck('name')
                ->toArray();

            $role->syncPermissions($permissions);

            $this->dispatch('refreshDatatable');
            $this->dispatch('update');
            $this->resetInput();
        });
    }

    public function delete($id)
    {
        try {
            $role = Role::findOrFail($id);

            if ($role->users()->exists()) {
                $this->dispatch('errorDelete');
            }
            
            $role->delete();
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            dd($e);
        }
    }
}