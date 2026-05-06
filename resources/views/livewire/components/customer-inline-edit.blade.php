<div class="modal fade" id="modal-inline-edit" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title mb-0">
                    ✏️ Editar cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="updateInline">
                <div class="modal-body">

                    {{-- NOMBRE --}}
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               wire:model.defer="name">
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- TELEFONO --}}
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text"
                               class="form-control @error('telephone') is-invalid @enderror"
                               wire:model.defer="telephone">
                        @error('telephone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- DIRECCION --}}
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text"
                               class="form-control @error('inline_address') is-invalid @enderror"
                               wire:model.defer="address">
                        @error('inline_address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- REFERENCIA --}}
                    <div class="form-group">
                        <label>Referencia</label>
                        <input type="text"
                               class="form-control"
                               wire:model.defer="inline_reference"
                               placeholder="Conjunto Arboleda Torre 1 Apto 1008">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button class="btn btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
