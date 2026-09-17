<div class="modal fade" id="modal-view" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    Detalle de Devolución
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="col-md-6">
                        <label class="font-weight-bold">Fecha</label>
                        <input type="text" class="form-control" value="{{ $return_date }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="font-weight-bold">Nota crédito</label>
                        <input type="text" class="form-control" value="{{ $credit_note_number }}" disabled>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label class="font-weight-bold">Motivo</label>
                    <textarea class="form-control" rows="2" disabled>{{ $reason }}</textarea>
                </div>

                @include('inventory.purchase-returns._lines')

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>
