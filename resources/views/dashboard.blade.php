<div>
    <div class="container-fluid">

        {{-- FILTRO --}}
        <div class="mb-3">
            <select wire:model.live="range" class="form-control w-auto">
                <option value="today">Hoy</option>
                <option value="week">Últimos 7 días</option>
                <option value="month">Este mes</option>
            </select>
        </div>

        {{-- KPIs --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card p-3">
                    <h6>Ventas</h6>
                    <h4>${{ number_format($kpis['sales'],0,',','.') }}</h4>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3">
                    <h6>Pedidos</h6>
                    <h4>{{ $kpis['orders'] }}</h4>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3">
                    <h6>Ticket Promedio</h6>
                    <h4>${{ number_format($kpis['avg'],0,',','.') }}</h4>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3">
                    <h6>Crecimiento</h6>
                    <h4 class="{{ $kpis['growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($kpis['growth'],2) }}%
                    </h4>
                </div>
            </div>
        </div>

        {{-- GRÁFICOS --}}
        <div class="row">

            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Ventas por día</h5>
                    <div wire:ignore id="dailyChart"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Ventas por mes</h5>
                    <div wire:ignore id="monthlyChart"></div>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="card p-3">
                    <h5>Top productos</h5>
                    <div wire:ignore id="topChart"></div>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="card p-3">
                    <h5>Productos con baja rotación</h5>
                    <div wire:ignore id="lowChart"></div>
                </div>
            </div>

        </div>
    </div>

    @push('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
    document.addEventListener('livewire:init', () => {

        Livewire.hook('morph.updated', () => renderCharts());

        function renderCharts() {

            const daily = @json($dailySales);
            const monthly = @json($monthlySales);
            const top = @json($topProducts);
            const low = @json($lowProducts);

            // limpiar
            ['dailyChart','monthlyChart','topChart','lowChart']
                .forEach(id => document.getElementById(id).innerHTML = '');

            // ===== DAILY =====
            new ApexCharts(document.querySelector("#dailyChart"), {
                chart: { type: 'line', height: 300 },
                series: [{
                    name: 'Ventas',
                    data: daily.map(d => d.total)
                }],
                xaxis: { categories: daily.map(d => d.date) }
            }).render();

            // ===== MONTHLY =====
            new ApexCharts(document.querySelector("#monthlyChart"), {
                chart: { type: 'area', height: 300 },
                series: [{
                    name: 'Ventas',
                    data: monthly.map(m => m.total)
                }],
                xaxis: { categories: monthly.map(m => m.mes) }
            }).render();

            // ===== TOP =====
            new ApexCharts(document.querySelector("#topChart"), {
                chart: { type: 'bar', height: 300 },
                series: [{
                    name: 'Cantidad',
                    data: top.map(t => t.total)
                }],
                xaxis: { categories: top.map(t => t.name) }
            }).render();

            // ===== LOW =====
            new ApexCharts(document.querySelector("#lowChart"), {
                chart: { type: 'bar', height: 300 },
                series: [{
                    name: 'Cantidad',
                    data: low.map(t => t.total)
                }],
                xaxis: { categories: low.map(t => t.name) }
            }).render();
        }

        renderCharts();
    });
    </script>
    @endpush
</div>