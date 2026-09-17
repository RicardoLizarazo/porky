<div class="modal fade" id="modal-edit" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>
                    Editar Proveedor
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="update">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Nombre</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   wire:model.defer="name">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="font-weight-bold">NIT</label>
                            <input type="text"
                                   class="form-control @error('nit') is-invalid @enderror"
                                   wire:model.defer="nit">
                            @error('nit') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Persona de contacto</label>
                            <input type="text"
                                   class="form-control @error('contact_name') is-invalid @enderror"
                                   wire:model.defer="contact_name">
                            @error('contact_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="font-weight-bold">Teléfono</label>
                            <input type="text"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   wire:model.defer="phone">
                            @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   wire:model.defer="email">
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="font-weight-bold">Estado</label>
                            <select class="form-control" wire:model.defer="status">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label class="font-weight-bold">Dirección</label>
                        <input type="text"
                               class="form-control @error('address') is-invalid @enderror"
                               wire:model.defer="address">
                        @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Notas</label>
                        <textarea class="form-control" rows="2" wire:model.defer="notes"></textarea>
                    </div>

                    @include('inventory.suppliers._items-checklist')

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>

        </div>
    </div>
</div>
