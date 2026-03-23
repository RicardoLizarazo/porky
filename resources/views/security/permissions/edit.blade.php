<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="editPermissionModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit.prevent="update">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPermissionModalLabel">
                        <i class="fas fa-edit mr-2"></i> Editar Permiso
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Campo Nombre -->
                        <div class="form-group">
                            <label for="edit-permission-name" class="font-weight-bold">Nombre del Permiso</label>
                            <input type="text" id="edit-permission-name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Ej: users.update" 
                                   wire:model.defer="name"
                                   aria-describedby="nameHelp">
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </div>
                            @enderror
                            <small id="nameHelp" class="form-text text-muted">
                                Usar formato snake_case (ej: module.action)
                            </small>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="form-group mt-4">
                            <label for="edit-permission-description" class="font-weight-bold">Descripción</label>
                            <textarea id="edit-permission-description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" 
                                      wire:model.defer="description"
                                      placeholder="Describa el propósito de este permiso"
                                      aria-describedby="descriptionHelp"></textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </div>
                            @enderror
                            <small id="descriptionHelp" class="form-text text-muted">
                                Máximo 255 caracteres
                            </small>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove wire:target="update">
                            <i class="fas fa-save mr-1"></i> Actualizar
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>