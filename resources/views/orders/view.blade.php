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

            {{-- CLIENTE O MESA --}}
            <div class="card mb-3 shadow-sm">
                <div class="card-body py-2">
                    <div class="row">
            
                        @if($is_table_order)
            
                            <div class="col-md-4">
                                <small class="text-muted">Mesa</small><br>
                                <strong>
                                    <i class="fas fa-utensils mr-1 text-danger"></i>
                                    {{ $table_name }}
                                </strong>
                                @if($floor_name)
                                    <br><small class="text-muted">{{ $floor_name }}</small>
                                @endif
                            </div>
            
                            <div class="col-md-4">
                                <small class="text-muted">Mesero</small><br>
                                <strong>
                                    <i class="fas fa-user-tie mr-1 text-primary"></i>
                                    {{ $waiter_name }}
                                </strong>
                            </div>
            
                        @else
            
                            <div class="col-md-4">
                                <small class="text-muted">Cliente</small><br>
                                <strong>{{ $customer_name ?? 'N/A' }}</strong>
                            </div>
            
                            <div class="col-md-4">
                                <small class="text-muted">Telefono</small><br>
                                @if($customer_phone)
                                    <strong>
                                        <i class="fas fa-phone-alt mr-1 text-success"></i>
                                        {{ $customer_phone }}
                                    </strong>
                                @else
                                    <strong>N/A</strong>
                                @endif
                            </div>
            
                        @endif
            
                        <div class="col-md-4 text-md-right mt-2 mt-md-0">
                            <small class="text-muted">Fecha</small><br>
                            <strong>{{ $ordered_at }}</strong>
                        </div>
            
                    </div>
                </div>
            </div>

                {{-- INFO PEDIDO --}}
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
                                @if($is_table_order)
                                    <span class="badge badge-{{ $is_paid ? 'success' : 'warning' }}">
                                        {{ $is_paid ? 'Pagado' : 'Pendiente' }}
                                    </span>
                                @else
                                    <strong>{{ ucfirst($payment_method) }}</strong>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

@if($is_table_order)

    {{-- FORMAS DE PAGO --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body py-2">

            <small class="text-muted">Formas de pago</small>

            @forelse($payments as $pay)
                <div class="d-flex justify-content-between border-bottom py-1">
                    <strong>
                        <i class="fas fa-money-bill-wave mr-1 text-success"></i>
                        {{ $pay['method'] }}
                    </strong>
                    <span>$ {{ number_format($pay['amount'], 0, ',', '.') }}</span>
                </div>
            @empty
                <div class="py-1">
                    <span class="badge badge-warning">
                        <i class="fas fa-clock mr-1"></i> Pendiente de pago
                    </span>
                </div>
            @endforelse

            @if($tip > 0)
                <div class="d-flex justify-content-between pt-2">
                    <small class="text-muted">Propina</small>
                    <small class="text-muted">
                        $ {{ number_format($tip, 0, ',', '.') }}
                    </small>
                </div>
            @endif

        </div>
    </div>

@else

    {{-- ENTREGA --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body py-2">
            <div class="row">

                <div class="col-md-6">
                    <small class="text-muted">Domiciliario</small><br>
                    <strong>{{ $delivery_name ?? 'Sin asignar' }}</strong>
                </div>

                <div class="col-md-6 mt-2 mt-md-0">
                    <small class="text-muted">Direccion</small><br>
                    <strong>{{ $customer_address ?? 'Sin dirección' }}</strong>
                </div>

            </div>
        </div>
    </div>

@endif

@if($is_table_order && !empty($cancelledItems))

    {{-- PRODUCTOS CANCELADOS --}}
    <div class="card mb-3 shadow-sm border-danger">
        <div class="card-body py-2">

            <small class="text-danger font-weight-bold">
                <i class="fas fa-ban mr-1"></i> Productos cancelados (ya enviados a cocina)
            </small>

            @foreach($cancelledItems as $item)
                <div class="d-flex justify-content-between align-items-start border-bottom py-1">
                    <div>
                        <strong>{{ $item['quantity'] }}x {{ $item['product_name'] }}</strong>
                        <br>
                        <small class="text-muted">
                            Cancelado por {{ $item['cancelled_by'] ?? 'Sin registro' }}
                            — {{ $item['cancelled_at'] }}
                        </small>
                    </div>
                    <span class="text-muted">
                        $ {{ number_format($item['subtotal'], 0, ',', '.') }}
                    </span>
                </div>
            @endforeach

        </div>
    </div>

@endif

                <hr>

                {{-- DETALLE --}}
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
                                                {{ $item['comment'] }}
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

                {{-- TOTALES --}}
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