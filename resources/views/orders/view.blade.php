<div class="modal fade" id="modal-view" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Pedido #{{ $order_id }}
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                {{-- 🔹 CLIENTE Y FECHA --}}
                <div class="card mb-3 shadow-sm">
                    <div class="card-body py-2">
                        <div class="row">

                            <div class="col-md-4">
                                <small class="text-muted">Cliente</small><br>
                                <strong>{{ $customer_name ?? 'N/A' }}</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Teléfono</small><br>

                                @if($customer_phone)
                                    <strong>
                                        <i class="fas fa-phone-alt mr-1 text-success"></i>
                                        {{ $customer_phone }}
                                    </strong>
                                @else
                                    <strong>N/A</strong>
                                @endif

                            </div>

                            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                                <small class="text-muted">Fecha</small><br>
                                <strong>{{ $ordered_at }}</strong>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- 🔹 INFO PEDIDO --}}
                <div class="card mb-3 shadow-sm">
                    <div class="card-body py-2">
                        <div class="row text-center text-md-left">

                            <div class="col-md-4 mb-2 mb-md-0">
                                <small class="text-muted">Tipo</small><br>
                                <strong>{{ $type_name ?? 'N/A' }}</strong>
                            </div>

                            <div class="col-md-4 mb-2 mb-md-0">
                                <small class="text-muted">Estado</small><br>
                                <span class="badge badge-{{ $status_color }}">
                                    {{ $status_name }}
                                </span>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Pago</small><br>
                                <strong>{{ ucfirst($payment_method) }}</strong>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- 🔹 ENTREGA --}}
                <div class="card mb-3 shadow-sm">
                    <div class="card-body py-2">
                        <div class="row">

                            <div class="col-md-6">
                                <small class="text-muted">Domiciliario</small><br>
                                <strong>{{ $delivery_name ?? 'Sin asignar' }}</strong>
                            </div>

                            <div class="col-md-6 mt-2 mt-md-0">
                                <small class="text-muted">Dirección</small><br>
                                <strong>{{ $customer_address ?? 'Sin dirección' }}</strong>
                            </div>

                        </div>
                    </div>
                </div>

                <hr>

                {{-- 🛒 DETALLE --}}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Producto</th>
                                <th width="80">Cant</th>
                                <th width="120">Precio</th>
                                <th width="120">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details as $item)
                                <tr>
                                    <td>
                                        {{ $item['name'] }}
                                        @if(!empty($item['comment']))
                                            <br>
                                            <small class="text-muted">
                                                📝 {{ $item['comment'] }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item['qty'] }}</td>
                                    <td class="text-right">
                                        $ {{ number_format($item['price'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        $ {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Sin productos
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 💰 TOTALES --}}
                <div class="row mt-3">
                    <div class="col-md-6">
                        @if($comment)
                            <strong>Comentario:</strong><br>
                            <small>{{ $comment }}</small>
                        @endif

                        @if($indication)
                            <br>
                            <strong>Indicaciones:</strong><br>
                            <small>{{ $indication }}</small>
                        @endif
                    </div>

                    <div class="col-md-6 text-right">

                        {{-- Subtotal productos --}}
                        <div>
                            <strong>Productos:</strong>
                            $ {{ number_format($subtotal, 0, ',', '.') }}
                        </div>

                        {{-- Empaque --}}
                        @if(!empty($packaging_total) && $packaging_total > 0)
                            <div class="text-muted">
                                <strong>Empaque:</strong>
                                $ {{ number_format($packaging_total, 0, ',', '.') }}
                            </div>
                        @endif

                        {{-- Domicilio --}}
                        @if(!empty($delivery_cost) && $delivery_cost > 0)
                            <div class="text-muted">
                                <strong>Domicilio:</strong>
                                $ {{ number_format($delivery_cost, 0, ',', '.') }}
                            </div>
                        @endif

                        <hr class="my-2">

                        {{-- Total --}}
                        <div class="h5">
                            <strong>Total:</strong>
                            $ {{ number_format($total, 0, ',', '.') }}
                        </div>

                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-cef btn-cef-cancel" data-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>