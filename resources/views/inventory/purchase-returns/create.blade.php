<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-undo mr-2"></i>
                    Nueva Devolución a Proveedor
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="store">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Compra (factura)</label>
                            <select class="form-control @error('purchase_id') is-invalid @enderror"
                                    wire:model.live="purchase_id">
                                <option value="">Seleccione...</option>
                                @foreach($confirmedPurchases as $purchase)
                                    <option value="{{ $purchase->id }}">
                                        {{ $purchase->supplier->name }} — Factura {{ $purchase->invoice_number }} ({{ $purchase->purchase_date->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('purchase_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="font-weight-bold">Fecha</label>
                            <input type="date"
                                   class="form-control @error('return_date') is-invalid @enderror"
                                   wire:model.defer="return_date">
                            @error('return_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="font-weight-bold">Nota crédito (opcional)</label>
                            <input type="text" class="form-control" wire:model.defer="credit_note_number">
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label class="font-weight-bold">Motivo</label>
                        <textarea class="form-control" rows="2" wire:model.defer="reason"></textarea>
                    </div>

                    @include('inventory.purchase-returns._lines')

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar devolución</button>
                </div>
            </form>

        </div>
    </div>
</div>
