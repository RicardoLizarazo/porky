<div>

    <!-- FILTROS -->
    <div class="d-flex gap-2 mb-3">
        <input type="date" wire:model="from" class="form-control">
        <input type="date" wire:model="to" class="form-control">

        <select wire:model="report" class="form-control">
            <option value="products">Productos</option>
            <option value="customers">Clientes</option>
            <option value="delivery">Domiciliarios</option>
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

        </div>
    </div>

</div>
