<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-tags mr-2"></i>
                    Nueva Categoría
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form wire:submit.prevent="store">
                <div class="modal-body">

                    {{-- NOMBRE --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Ej: Bebidas, Hamburguesas..."
                               wire:model.defer="name">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Descripción</label>
                        <textarea
                            class="form-control @error('description') is-invalid @enderror"
                            rows="3"
                            wire:model.defer="description"></textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ESTADO --}}
                    <div class="row">

                        {{-- VISIBLE --}}
                        <div class="col-md-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                    class="custom-control-input"
                                    id="visible_create"
                                    wire:model.defer="is_visible">
                                <label class="custom-control-label" for="visible_create">
                                    Visible al cliente
                                </label>
                            </div>
                        </div>

                        {{-- ACTIVO --}}
                        <div class="col-md-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                    class="custom-control-input"
                                    id="active_create"
                                    wire:model.defer="is_active">
                                <label class="custom-control-label" for="active_create">
                                    Activa
                                </label>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-cef btn-cef-cancel" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-cef btn-cef-create">
                        <span wire:loading.remove wire:target="store">
                            <i class="fas fa-save mr-1"></i>
                            Guardar
                        </span>
                        <span wire:loading wire:target="store">
                            <span class="spinner-border spinner-border-sm"></span>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>