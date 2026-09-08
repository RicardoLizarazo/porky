<div class="modal fade" id="modal-edit" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>
                    Editar Producto de Inventario
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
                            <label class="font-weight-bold">Código (opcional)</label>
                            <input type="text"
                                   class="form-control @error('code') is-invalid @enderror"
                                   wire:model.defer="code">
                            @error('code') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Tipo</label>
                            <select class="form-control @error('type') is-invalid @enderror" wire:model.defer="type">
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="font-weight-bold">Unidad base (existencias)</label>
                            <select class="form-control @error('base_unit_id') is-invalid @enderror" wire:model.live="base_unit_id">
                                <option value="">Seleccione...</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                @endforeach
                            </select>
                            @error('base_unit_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="text-muted">Cambiar la unidad base no convierte el stock existente.</small>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Stock mínimo (opcional)</label>
                            <input type="number" step="0.01" min="0"
                                   class="form-control @error('min_stock') is-invalid @enderror"
                                   wire:model.defer="min_stock">
                            @error('min_stock') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="font-weight-bold">Estado</label>
                            <select class="form-control" wire:model.defer="is_active">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label class="font-weight-bold">Notas</label>
                        <textarea class="form-control" rows="2" wire:model.defer="notes"></textarea>
                    </div>

                    @if($base_unit_id)
                        @include('inventory.inventory-items._alt-units')
                    @endif

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>

        </div>
    </div>
</div>
