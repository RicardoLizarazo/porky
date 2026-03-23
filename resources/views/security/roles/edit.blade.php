<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit.prevent="update">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">
                        <i class="fas fa-edit mr-2"></i> Editar Rol
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Campo Nombre -->
                        <div class="form-group">
                            <label for="edit-name" class="font-weight-bold">Nombre del Rol</label>
                            <input type="text" id="edit-name" class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Ej: Administrador" wire:model.defer="name">
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Campo Descripción -->
                        <div class="form-group mt-4">
                            <label for="edit-description" class="font-weight-bold">Descripción</label>
                            <textarea id="edit-description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" wire:model.defer="description"
                                      placeholder="Describa las funciones y permisos de este rol"></textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </div>
                            @enderror
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
                                                                
                                                                // Traducciones de módulos (completas)
                                                                $modules = [
                                                                    'employees'         => 'Empleados',
                                                                    'users'             => 'Usuarios',
                                                                    'roles'             => 'Roles',
                                                                    'permissions'       => 'Permisos',
                                                                    'approval_profiles' => 'Perfiles de aprobación',
                                                                    'audit_logs'        => 'Registros de auditoría',
                                                                    'dashboard'         => 'Panel',
                                                                    'request_exit'      => 'Solicitudes de Salida',
                                                                    'settings'          => 'Configuración',
                                                                    'reports'           => 'Reportes',
                                                                    'system'            => 'Sistema',
                                                                    'exit_types'        => 'Tipos de Salida',
                                                                ];
                                                                
                                                                // Traducciones específicas de acciones (completas con tus permisos)
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
                        <span wire:loading.remove wire:target="update">
                            <i class="fas fa-save mr-1"></i> Actualizar
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Procesando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>