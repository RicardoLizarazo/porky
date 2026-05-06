<div>

    <!-- FILTROS -->
    <div class="d-flex gap-2 mb-3">
        <input type="date" wire:model="from" class="form-control">
        <input type="date" wire:model="to" class="form-control">

        <select wire:model="report" class="form-control">
            <option value="products">Productos</option>
            <option value="customers">Clientes</option>
            <option value="delivery">Domiciliarios</option>
            <option value="delivery_detail">Domicilios Detallado</option>
            <option value="payments">Pagos</option>
        </select>

        <button wire:click="generate" class="btn btn-primary">
            Generar
        </button>

        <button wire:click="export" class="btn btn-success">
            Exportar
        </button>
    </div>

    <!-- TABLA DINÁMICA -->
    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-sm">
                <thead>
                    <tr>
                        @if($report === 'products')
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Promedio</th>
                        @endif

                        @if($report === 'customers')
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Pedidos</th>
                            <th>Total</th>
                            <th>Promedio</th>
                        @endif

                        @if($report === 'delivery')
                            <th>Domiciliario</th>
                            <th>Tipo de pago</th>
                            <th>Pedidos</th>
                            <th>Total</th>
                        @endif

                        @if($report === 'payments')
                            <th>Método</th>
                            <th>Pedidos</th>
                            <th>Total</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $row)
                        <tr>

                            @if($report === 'products')
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->quantity }}</td>
                                <td>${{ number_format($row->total, 0) }}</td>
                                <td>${{ number_format($row->avg_price, 0) }}</td>
                            @endif

                            @if($report === 'customers')
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->orders }}</td>
                                <td>${{ number_format($row->total, 0) }}</td>
                                <td>${{ number_format($row->avg, 0) }}</td>
                            @endif

                            @if($report === 'delivery')
                                <td>{{ $row->delivery }}</td>
                                <td>{{ $row->payment_method }}</td>
                                <td>{{ $row->orders }}</td>
                                <td>${{ number_format($row->total, 0) }}</td>
                            @endif

                            @if($report === 'payments')
                                <td>{{ $row->payment_method }}</td>
                                <td>{{ $row->orders }}</td>
                                <td>${{ number_format($row->total, 0) }}</td>
                            @endif

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                Sin datos
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            @if($report === 'delivery_detail')

            <div class="row">
                @forelse($data as $delivery => $orders)

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">

                            <!-- HEADER -->
                            <div class="card-header bg-dark text-white">
                                <strong>{{ $delivery }}</strong><br>
                                <small>
                                    {{ $orders->count() }} pedidos |
                                    ${{ number_format($orders->sum('total'), 0) }}
                                </small>
                            </div>

                            <!-- BODY -->
                            <div class="card-body p-2" style="max-height: 300px; overflow:auto;">

                                @foreach($orders as $order)
                                    <div class="border-bottom mb-2 pb-1">

                                        <div class="d-flex justify-content-between">
                                            <strong>#{{ $order->id }}</strong>
                                            <span>${{ number_format($order->total, 0) }}</span>
                                        </div>

                                        <div class="small text-muted">
                                            {{ $order->customer }}
                                        </div>

                                        <div class="small">
                                            {{ $order->payment_method }}
                                        </div>

                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                @empty
                    <div class="col-12 text-center">
                        Sin datos
                    </div>
                @endforelse
            </div>

            @endif

        </div>
    </div>

</div> 
