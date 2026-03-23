<div class="modal fade" id="modal-create" tabindex="-1" role="dialog" aria-labelledby="createPermissionModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit.prevent="store">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPermissionModalLabel">
                        <i class="fas fa-key mr-2"></i> Nuevo Permiso
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Campo Nombre -->
                        <div class="form-group">
                            <label for="permission-name" class="font-weight-bold">Nombre del Permiso</label>
                            <input type="text" id="permission-name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Ej: users.create" 
                                   wire:model.defer="name"
                                   aria-describedby="nameHelp">
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </div>
                            @enderror
                            <small id="nameHelp" class="form-text text-muted">
                                Usa formato snake_case (ej: module.action)
                            </small>
                        </div>

                        <!-- Campo Descripcion -->
                        <div class="form-group mt-4">
                            <label for="permission-description" class="font-weight-bold">Descripción</label>
                            <textarea id="permission-description" 
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
                                Máximo 255 caracteres. Describe qué permite hacer este permiso.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
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