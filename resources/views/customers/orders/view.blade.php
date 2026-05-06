<div wire:ignore.self class="modal fade" id="modal-customer-view" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Pedido #{{ $order_id }}
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <div class="modal-body">

                {{-- ESTADO --}}
                <div class="text-center mb-3">
                    <span class="badge badge-{{ $status_color }} p-2">
                        {{ $status_name }}
                    </span>
                </div>

                {{-- INFO --}}
                <div class="mb-3">
                    <strong>Fecha:</strong> {{ $ordered_at }} <br>
                    <strong>Dirección:</strong> {{ $customer_address }} <br>
                    <strong>Domiciliario:</strong> {{ $delivery_name ?? 'Asignando...' }}
                </div>

                <hr>

                {{-- ITEMS --}}
                @foreach($details as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            {{ $item['name'] }} x{{ $item['qty'] }}
                        </div>
                        <div>
                            ${{ number_format($item['subtotal'],0,',','.') }}
                        </div>
                    </div>
                @endforeach

                <hr>

                {{-- TOTALES --}}
                <div class="text-right">
                    <div>Subtotal: ${{ number_format($subtotal,0,',','.') }}</div>
                    <div>Empaque: ${{ number_format($packaging_total,0,',','.') }}</div>
                    <div>Domicilio: ${{ number_format($delivery_cost,0,',','.') }}</div>
                    <h5>Total: ${{ number_format($total,0,',','.') }}</h5>
                </div>

            </div>

        </div>
    </div>
</div>