{{-- Líneas de la compra --}}
<hr>
<h5>Líneas de la compra</h5>

@error('details') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

@if(!$supplier_id)
    <p class="text-muted small">Seleccione primero un proveedor.</p>
@elseif(empty($supplierItems) || (is_countable($supplierItems) && count($supplierItems) === 0))
    <p class="text-muted small">
        Este proveedor no tiene productos vinculados. Edítelo desde "Proveedores" para asignarle productos.
    </p>
@else
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th style="min-width:200px;">Producto</th>
                    <th style="min-width:140px;">Unidad</th>
                    <th style="width:120px;">Cantidad</th>
                    <th style="width:140px;">Costo unitario</th>
                    <th style="width:120px;">Subtotal</th>
                    @if(!$readOnly)
                        <th style="width:40px;"></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($details as $index => $row)
                    <tr wire:key="detail-{{ $index }}">
                        <td>
                            <select class="form-control form-control-sm @error('details.'.$index.'.inventory_item_id') is-invalid @enderror"
                                    wire:model.live="details.{{ $index }}.inventory_item_id"
                                    {{ $readOnly ? 'disabled' : '' }}>
                                <option value="">Seleccione...</option>
                                @foreach($supplierItems as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control form-control-sm @error('details.'.$index.'.unit_id') is-invalid @enderror"
                                    wire:model.defer="details.{{ $index }}.unit_id"
                                    {{ $readOnly ? 'disabled' : '' }}>
                                <option value="">Unidad...</option>
                                @foreach($this->unitOptionsForItem($row['inventory_item_id']) as $unit)
                                    <option value="{{ $unit['id'] }}">{{ $unit['label'] }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.0001" min="0"
                                   class="form-control form-control-sm @error('details.'.$index.'.quantity') is-invalid @enderror"
                                   wire:model.live.debounce.400ms="details.{{ $index }}.quantity"
                                   {{ $readOnly ? 'disabled' : '' }}>
                        </td>
                        <td>
                            <input type="number" step="0.0001" min="0"
                                   class="form-control form-control-sm @error('details.'.$index.'.unit_cost') is-invalid @enderror"
                                   wire:model.live.debounce.400ms="details.{{ $index }}.unit_cost"
                                   {{ $readOnly ? 'disabled' : '' }}>
                        </td>
                        <td class="align-middle">
                            ${{ number_format(($row['quantity'] ?: 0) * ($row['unit_cost'] ?: 0), 2) }}
                        </td>
                        @if(!$readOnly)
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeDetail({{ $index }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!$readOnly)
        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addDetail">
            + Agregar línea
        </button>
    @endif

    <div class="text-right mt-2">
        <strong>Total: ${{ number_format($formTotal, 2) }}</strong>
    </div>
@endif
