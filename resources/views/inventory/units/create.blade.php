<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-ruler mr-2"></i>
                    Nueva Unidad de Medida
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="store">
                <div class="modal-body">

                    <div class="form-group">
                        <label class="font-weight-bold">Nombre</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Ej: Libra, Gramo, Bulto..."
                               wire:model.defer="name">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Abreviatura</label>
                        <input type="text"
                               class="form-control @error('abbreviation') is-invalid @enderror"
                               placeholder="Ej: lb, g, bulto"
                               wire:model.defer="abbreviation">
                        @error('abbreviation')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>

        </div>
    </div>
</div>
