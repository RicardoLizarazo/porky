<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Nueva Compra
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="store">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold">Proveedor</label>
                            <select class="form-control @error('supplier_id') is-invalid @enderror"
                                    wire:model.live="supplier_id">
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
                                   wire:model.defer="invoice_number">
                            @error('invoice_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="font-weight-bold">Fecha de compra</label>
                            <input type="date"
                                   class="form-control @error('purchase_date') is-invalid @enderror"
                                   wire:model.defer="purchase_date">
                            @error('purchase_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label class="font-weight-bold">Notas</label>
                        <textarea class="form-control" rows="2" wire:model.defer="notes"></textarea>
                    </div>

                    @include('inventory.purchases._details-form')

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar borrador</button>
                </div>
            </form>

        </div>
    </div>
</div>
