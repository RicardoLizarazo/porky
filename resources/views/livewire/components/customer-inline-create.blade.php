<div class="modal fade" id="modal-inline-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title mb-0">
                    ➕ Nuevo cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="storeInline">
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
                               class="form-control @error('address') is-invalid @enderror"
                               wire:model.defer="address"
                               placeholder="Ej: Calle 5 #10-20">
                        @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- REFERENCIA --}}
                    <div class="form-group">
                        <label>Referencia</label>
                        <input type="text"
                               class="form-control"
                               wire:model.defer="reference"
                               placeholder="Conjunto Arboleda Torre 1 Apto 1008">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button class="btn btn-success">
                        Guardar y usar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>