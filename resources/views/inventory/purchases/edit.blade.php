<div class="modal fade" id="modal-edit" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas {{ $readOnly ? 'fa-eye' : 'fa-edit' }} mr-2"></i>
                    {{ $readOnly ? 'Detalle de Compra' : 'Editar Compra' }}
                    @if($status === 'confirmed')
                        <span class="badge badge-success ml-2">Confirmada</span>
                    @elseif($status)
                        <span class="badge badge-warning ml-2">Borrador</span>
                    @endif
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="update">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold">Proveedor</label>
                            <select class="form-control @error('supplier_id') is-invalid @enderror"
                                    wire:model.live="supplier_id"
                                    {{ $readOnly ? 'disabled' : '' }}>
                                <option value="">Seleccione...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="font-weight-bold">Número de factura</label>
                            <input type="text"
                                   class="form-control @error('invoice_number') is-invalid @enderror"
                                   wire:model.defer="invoice_number"
                                   {{ $readOnly ? 'disabled' : '' }}>
                            @error('invoice_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="font-weight-bold">Fecha de compra</label>
                            <input type="date"
                                   class="form-control @error('purchase_date') is-invalid @enderror"
                                   wire:model.defer="purchase_date"
                                   {{ $readOnly ? 'disabled' : '' }}>
                            @error('purchase_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label class="font-weight-bold">Notas</label>
                        <textarea class="form-control" rows="2" wire:model.defer="notes" {{ $readOnly ? 'disabled' : '' }}></textarea>
                    </div>

                    @include('inventory.purchases._details-form')

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ $readOnly ? 'Cerrar' : 'Cancelar' }}
                    </button>

                    @if(!$readOnly)
                        <button type="button"
                                class="btn btn-success"
                                wire:click="$dispatch('confirmPurchase', { id: {{ $purchase_id }} })">
                            <i class="fas fa-check mr-1"></i> Confirmar compra
                        </button>

                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    @endif
                </div>
            </form>

        </div>
    </div>
</div>
