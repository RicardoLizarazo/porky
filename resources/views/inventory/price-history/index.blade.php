<div class="container-fluid">

    <div class="card mb-3">
        <div class="card-header">
            <h3>Precio más alto pagado por producto</h3>
        </div>
        <div class="card-body">
            @if($summary->isEmpty())
                <p class="text-muted mb-0">Aún no hay compras confirmadas para mostrar.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Proveedor más caro</th>
                                <th>Costo por unidad base</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary as $row)
                                <tr>
                                    <td>{{ $row->item_name }}</td>
                                    <td>{{ $row->supplier_name }}</td>
                                    <td>${{ number_format($row->unit_cost_base, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->purchase_date)->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Historial de compras</h3>
        </div>
        <div class="card-body">
            <livewire:inventory.price-history-table theme="bootstrap-4" />
        </div>
    </div>

</div>
