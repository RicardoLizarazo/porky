<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-dolly mr-2"></i>
                    Registrar Salida / Ajuste
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="store">
                <div class="modal-body">

                    <div class="form-group">
                        <label class="font-weight-bold">Producto</label>
                        <select class="form-control @error('inventory_item_id') is-invalid @enderror"
                                wire:model.live="inventory_item_id">
                            <option value="">Seleccione...</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }} (stock: {{ number_format($item->stock, 2) }} {{ $item->baseUnit->abbreviation ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('inventory_item_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Motivo</label>
                        <select class="form-control" wire:model.live="type">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($type === 'adjustment')
                        <div class="form-group">
                            <label class="font-weight-bold">Dirección del ajuste</label>
                            <select class="form-control" wire:model.live="direction">
                                <option value="decrease">Disminuir existencias</option>
                                <option value="increase">Aumentar existencias</option>
                            </select>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Cantidad</label>
                            <input type="number" step="0.0001" min="0"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   wire:model.defer="quantity">
                            @error('quantity') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        @if($this->isIncrease())
                            <div class="col-md-6">
                                <label class="font-weight-bold">Costo unitario</label>
                                <input type="number" step="0.0001" min="0"
                                       class="form-control @error('unit_cost') is-invalid @enderror"
                                       wire:model.defer="unit_cost">
                                @error('unit_cost') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Nota (quién y por qué)</label>
                        <textarea class="form-control @error('reason_note') is-invalid @enderror"
                                  rows="2"
                                  wire:model.defer="reason_note"></textarea>
                        @error('reason_note') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
