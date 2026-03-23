<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Support\Str;

class Permission extends SpatiePermission
{
    protected $appends = ['readable_name'];

    public function getReadableNameAttribute(): string
    {
        $name = $this->name ?? '';

        // Dividimos en módulo y acción
        $parts = explode('.', $name);
        $moduleKey = $parts[0] ?? null;
        $actionKey = $parts[1] ?? null;

        // Traducciones de módulos (completas)
        $modules = [
            'employees'         => 'Empleados',
            'users'             => 'Usuarios',
            'roles'             => 'Roles',
            'permissions'       => 'Permisos',
            'approval_profiles' => 'Perfiles de aprobación',
            'audit_logs'        => 'Registros de auditoría',
            'dashboard'         => 'Panel',
            'consultations'     => 'Consultas',
            'settings'          => 'Configuración',
            'reports'           => 'Reportes',
            'system'            => 'Sistema',
            'exit_types'        => 'Tipo de Salida',
            'contacts'          => 'Contactos',
            'specialists'       => 'Especialistas',
            'accounting'        => 'Contabilidad',
        ];

        // Traducciones específicas de acciones (completas)
        $actions = [
            // Acciones básicas
            'create'              => 'Crear',
            'edit'                => 'Editar',
            'delete'              => 'Eliminar',
            'view'                => 'Ver',
            'list'                => 'Listar',
            'show'                => 'Mostrar',
            'update'              => 'Actualizar',
            
            // Acciones específicas de tu sistema
            'import_excel'        => 'Importar desde Excel',
            'export_excel'        => 'Exportar a Excel',
            'export_pdf'          => 'Exportar a PDF',
            'send_pdf'            => 'Enviar PDF',
            'manage_sequence'     => 'Gestionar secuencia',
            'remove_area'         => 'Eliminar área',
            'add_area'            => 'Agregar área',
            'assign'              => 'Asignar',
            'assign_permissions'  => 'Asignar permisos',
            'assign_role'         => 'Asignar rol',
            'approve'             => 'Aprobar',
            'reject'              => 'Rechazar',
            'filter'              => 'Filtrar',
            'access'              => 'Acceder',
            'manage'              => 'Gestionar',
            'configure'           => 'Configurar',
            'deactivate'          => 'Desactivar',
        ];

        // Si es un permiso sin acción (solo módulo)
        if (!$actionKey) {
            return $modules[$moduleKey] ?? Str::title(str_replace(['_', '-'], ' ', $moduleKey));
        }

        // Traducción del módulo
        $moduleLabel = $modules[$moduleKey] ?? Str::title(str_replace(['_', '-'], ' ', $moduleKey));
        
        // Traducción de la acción
        $normalizedAction = Str::of($actionKey)
            ->replace([' ', '-'], '_')
            ->lower()
            ->__toString();
            
        $actionLabel = $actions[$normalizedAction] ?? Str::title(str_replace(['_', '-'], ' ', $actionKey));

        // Construcción final en español
        return "{$actionLabel} {$moduleLabel}";
    }
}