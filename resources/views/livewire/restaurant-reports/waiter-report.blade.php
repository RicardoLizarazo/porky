{{-- resources/views/livewire/restaurant-reports/waiter-report.blade.php --}}
<div class="container-fluid">

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>
                Filtros
            </h3>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-6 col-md-3">
                    <label>Sede</label>
                    <select class="form-control" wire:model.live="location_id">
                        <option value="">Todas las sedes</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label>Piso</label>
                    <select class="form-control" wire:model.live="floor_id">
                        <option value="">Todos los pisos</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label>Desde</label>
                    <input type="date" class="form-control" wire:model.live="date_from">
                </div>

                <div class="col-6 col-md-3">
                    <label>Hasta</label>
                    <input type="date" class="form-control" wire:model.live="date_to">
                </div>

            </div>
        </div>
    </div>

    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-tie mr-2"></i>
                Ventas por Mesero
            </h3>

            <div class="card-tools">
                <button wire:click="exportExcel" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel mr-1"></i>
                    Exportar Excel
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Mesero</th>
                        <th class="text-right">Pedidos</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesByWaiter as $row)
                        <tr>
                            <td>{{ $row->user_name ?? 'Sin usuario' }}</td>
                            <td class="text-right">{{ $row->orders_count }}</td>
                            <td class="text-right">
                                $ {{ number_format($row->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Sin información</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>