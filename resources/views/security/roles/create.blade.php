<div class="modal fade" id="modal-create" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form wire:submit.prevent="store">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateLabel">
                        <i class="fas fa-plus-circle mr-2"></i> Nuevo Rol
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Campo Nombre -->
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nombre del Rol</label>
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Ej: Administrador" wire:model.defer="name">
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                Use un nombre descriptivo para el rol (solo letras, espacios y guiones)
                            </small>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="form-group mt-4">
                            <label for="description" class="font-weight-bold">Descripción</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" wire:model.defer="description"
                                      placeholder="Describa las funciones y permisos de este rol"></textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                Máximo 255 caracteres
                            </small>
                        </div>

                        <!-- Sección de Permisos -->
                        <div class="form-group mt-4">
                            <label class="font-weight-bold">Permisos</label>
                            <small class="form-text text-muted d-block mb-3">
                                Seleccione los permisos que tendrá este rol
                            </small>

                            @foreach($this->groupedPermissions as $module => $permissions)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>{{ $module }}</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($permissions as $permission)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            wire:model="selectedPermissions"
                                                            value="{{ $permission->id }}"
                                                            id="perm-{{ $permission->id }}">
                                                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                            @php
                                                                // Traducción directa en la vista como fallback
                                                                $name = $permission->name;
                                                                $parts = explode('.', $name);
                                                                $moduleKey = $parts[0] ?? '';
                                                                $actionKey = $parts[1] ?? '';

                                                                $modules = [
                                                                    'employees'         => 'Empleados',
                                                                    'users'             => 'Usuarios',
                                                                    'roles'             => 'Roles',
                                                                    'permissions'       => 'Permisos',
                                                                    'approval_profiles' => 'Perfiles de aprobación',
                                                                    'audit_logs'        => 'Registros de auditoría',
                                                                    'dashboard'         => 'Panel',
                                                                    'accounting'        => 'Contabilidad',
                                                                    'settings'          => 'Configuración',
                                                                    'reports'           => 'Reportes',
                                                                    'consultations'     => 'Consultas',
                                                                    'contacts'          => 'Contactos',
                                                                    'specialists'       => 'Especialistas',
                                                                    'system'            => 'Sistema',
                                                                    'exit_types'        => 'Tipos de Salida',
                                                                ];

                                                                $actions = [
                                                                    'create'              => 'Crear',
                                                                    'edit'                => 'Editar',
                                                                    'delete'              => 'Eliminar',
                                                                    'view'                => 'Ver',
                                                                    'list'                => 'Listar',
                                                                    'show'                => 'Mostrar',
                                                                    'update'              => 'Actualizar',
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

                                                                $moduleTrans = $modules[$moduleKey] ?? ucfirst($moduleKey);
                                                                $actionTrans = $actions[$actionKey] ?? ucfirst($actionKey);

                                                                echo $actionTrans && $moduleTrans ? "{$actionTrans} {$moduleTrans}" : $name;
                                                            @endphp
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if(count($this->groupedPermissions) === 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> No hay permisos agrupados disponibles.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove wire:target="store">
                            <i class="fas fa-save mr-1"></i> Guardar
                        </span>
                        <span wire:loading wire:target="store">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Procesando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
