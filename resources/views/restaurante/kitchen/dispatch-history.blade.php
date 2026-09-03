<div>

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>
                Historial de Despachos
            </h3>
        </div>

        <div class="card-body border-bottom">
            <div class="row">

                <div class="col-md-3">
                    <label class="font-weight-bold small">Buscar # de pedido</label>
                    <input type="number" class="form-control" wire:model.live.debounce.400ms="orderSearch" placeholder="Ej: 10630">
                    @if($orderSearch !== '')
                        <small class="text-muted">Ignorando el rango de fechas mientras buscas por pedido.</small>
                    @endif
                </div>

                <div class="col-md-3">
                    <label class="font-weight-bold small">Desde</label>
                    <input type="date" class="form-control" wire:model.live="dateFrom" @disabled($orderSearch !== '')>
                </div>

                <div class="col-md-3">
                    <label class="font-weight-bold small">Hasta</label>
                    <input type="date" class="form-control" wire:model.live="dateTo" @disabled($orderSearch !== '')>
                </div>

                <div class="col-md-3">
                    <label class="font-weight-bold small">Estacion</label>
                    <select class="form-control" wire:model.live="stationFilter">
                        <option value="">Todas</option>
                        @foreach($stations as $station)
                            <option value="{{ $station->id }}">{{ $station->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mt-3">
                    <label class="font-weight-bold small">Estado</label>
                    <select class="form-control" wire:model.live="statusFilter">
                        <option value="">Todos</option>
                        <option value="ready">Despachado</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Pedido #</th>
                        <th>Fecha y hora</th>
                        <th>Estacion</th>
                        <th>Mesa / Piso</th>
                        <th>Despachado por</th>
                        <th>Contenido</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($events as $items)

                        @php
                            $first = $items->first();
                            $ticket = $first->kitchenOrder;
                        @endphp

                        <tr>
                            <td>
                                <strong>#{{ $ticket?->order_id }}</strong>
                            </td>

                            <td>
                                {{ $first->ready_at ? \Carbon\Carbon::parse($first->ready_at)->format('d/m/Y h:i A') : '' }}
                            </td>

                            <td>
                                <span class="badge badge-secondary">
                                    {{ $first->station?->name }}
                                </span>
                            </td>

                            <td>
                                Mesa {{ $ticket?->diningTable?->name }}
                                <br>
                                <small class="text-muted">{{ $ticket?->floor?->name }}</small>
                            </td>

                            <td>
                                {{ $first->resolvedBy?->name ?? 'Sin registro' }}
                            </td>

                            <td>
                                @foreach($items as $item)
                                    <div>
                                        <strong>{{ $item->quantity }}x</strong> {{ $item->product_name }}
                                    </div>
                                @endforeach
                            </td>

                            <td>
                                @if($first->status === 'ready')
                                    <span class="badge badge-success">Despachado</span>
                                @else
                                    <span class="badge badge-danger">Cancelado</span>
                                @endif
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay despachos en el rango seleccionado.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $events->links() }}
        </div>

    </div>

</div>