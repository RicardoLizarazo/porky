@php
    /*
    |--------------------------------------------------------------------------
    | PALETA DE COLORES PARA IDENTIFICAR PISOS
    |--------------------------------------------------------------------------
    | Cada piso tiene un color visualmente diferente.
    |
    | El rojo se evita deliberadamente porque está reservado para
    | identificar mesas OCUPADAS.
    |--------------------------------------------------------------------------
    */

    $floorPalette = [
        '#1565C0', // Azul
        '#7B1FA2', // Morado
        '#F9A825', // Dorado
        '#00897B', // Turquesa
        '#2E7D32', // Verde
        '#EF6C00', // Naranja
        '#3949AB', // Índigo
        '#00838F', // Cian
    ];

    /*
    |--------------------------------------------------------------------------
    | ÍCONOS DE LOS PISOS
    |--------------------------------------------------------------------------
    */

    $floorIcons = [
        'fa-building',
        'fa-door-open',
        'fa-store',
        'fa-umbrella-beach',
        'fa-glass-martini-alt',
        'fa-warehouse',
        'fa-tree',
        'fa-layer-group',
    ];

    /*
    |--------------------------------------------------------------------------
    | ÍNDICE DEL PISO
    |--------------------------------------------------------------------------
    */

    $floorIndex = function ($floorId) use ($floors) {

        $index = $floors->search(
            fn ($floor) => $floor->id == $floorId
        );

        return $index === false ? 0 : $index;
    };

    /*
    |--------------------------------------------------------------------------
    | COLOR DEL PISO
    |--------------------------------------------------------------------------
    |
    | Esta variable DEBE mantenerse porque el foreach de abajo
    | utiliza $floorColor($floor->id).
    |
    |--------------------------------------------------------------------------
    */

    $floorColor = function ($floorId) use (
        $floorPalette,
        $floorIndex
    ) {
        return $floorPalette[
            $floorIndex($floorId) % count($floorPalette)
        ];
    };

    /*
    |--------------------------------------------------------------------------
    | ÍCONO DEL PISO
    |--------------------------------------------------------------------------
    */

    $floorIcon = function ($floorId) use (
        $floorIcons,
        $floorIndex
    ) {
        return $floorIcons[
            $floorIndex($floorId) % count($floorIcons)
        ];
    };

    /*
    |--------------------------------------------------------------------------
    | PISO SELECCIONADO
    |--------------------------------------------------------------------------
    */

    $selectedFloorModel = $floors->firstWhere(
        'id',
        $selectedFloor
    );

    $selectedColor = $floorColor($selectedFloor);

    $selectedIcon = $floorIcon($selectedFloor);
@endphp

<div class="container-fluid px-2 px-md-3" wire:poll.5s>

    {{-- PISOS --}}
    <div class="floor-tabs-wrapper mb-3">
        <div class="floor-tabs-scroll">
            @foreach($floors as $floor)
                @php
                    $color = $floorColor($floor->id);
                    $icon = $floorIcon($floor->id);
                @endphp

                <button
                    wire:click="$set('selectedFloor', {{ $floor->id }})"
                    class="floor-chip"
                    @class(['active' => $selectedFloor == $floor->id])
                    style="
                        --floor-color: {{ $color }};
                        {{ $selectedFloor == $floor->id ? 'background:' . $color . ';border-color:' . $color . ';' : 'border-color:' . $color . ';' }}
                    "
                >
                    <i class="fas {{ $icon }} mr-2" style="color: {{ $selectedFloor == $floor->id ? '#fff' : $color }}"></i>

                    {{ $floor->name }}

                    @if($selectedFloor == $floor->id)
                        <i class="fas fa-check ml-2"></i>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- BANNER DE PISO ACTIVO --}}
    <div class="active-floor-banner mb-3" style="--floor-color: {{ $selectedColor }};">
        <div class="active-floor-banner-icon">
            <i class="fas {{ $selectedIcon }}"></i>
        </div>
        <div>
            <div class="active-floor-banner-label">Viendo mesas de</div>
            <div class="active-floor-banner-name">{{ optional($selectedFloorModel)->name ?? 'Piso' }}</div>
        </div>
        <div class="active-floor-banner-count">
            {{ $tables->count() }} {{ $tables->count() == 1 ? 'mesa' : 'mesas' }}
        </div>
    </div>

    {{-- LEYENDA DE ESTADOS --}}
    <div class="status-legend mb-4">
        <div class="status-legend-item"><span class="dot" style="background:#43a047"></span> Disponible</div>
        <div class="status-legend-item"><span class="dot" style="background:#c62828"></span> Ocupada</div>
        <div class="status-legend-item"><span class="dot" style="background:#ff9800"></span> Reservada</div>
        <div class="status-legend-item"><span class="dot" style="background:#039be5"></span> Limpieza</div>
        <div class="status-legend-item"><span class="dot" style="background:#424242"></span> Pago</div>
    </div>

    {{-- BANNER MODO UNIÓN --}}
    @if($mergingTableId)
        <div class="merge-mode-banner mb-4">
            <span><i class="fas fa-link mr-2"></i> Selecciona la mesa que quieres unir</span>
            <button type="button" wire:click="cancelMerge" class="btn btn-sm btn-light">Cancelar</button>
        </div>
    @endif

    {{-- GRID --}}
    <div class="row">

        @forelse($tables as $table)

            <div class="col-6 col-lg-4 mb-3">

                <div
                    @if($mergingTableId && $mergingTableId != $table->id)
                        wire:click="confirmMerge({{ $table->id }})"
                    @else
                        wire:click="openTable({{ $table->id }})"
                    @endif
                    class="table-pos-card"
                    style="--floor-color: {{ $selectedColor }};"
                    @class([
                        'status-available' => $table->operationalStatus() === 'available',
                        'status-occupied' => $table->operationalStatus() === 'occupied',
                        'status-reserved' => $table->operationalStatus() === 'reserved',
                        'status-cleaning' => $table->operationalStatus() === 'cleaning',
                        'status-payment' => $table->operationalStatus() === 'pending_payment',
                        'merge-origin' => $mergingTableId == $table->id,
                        'merge-target-hint' => $mergingTableId && $mergingTableId != $table->id,
                    ])
                >

                    {{-- BADGE DE PISO: mismo color que la pestaña/banner activos,
                         así el mesero confirma de un vistazo que está en el piso correcto --}}
                    <div class="table-floor-badge">
                        <i class="fas {{ $selectedIcon }}"></i>
                        {{ optional($selectedFloorModel)->name }}
                    </div>

                    {{-- BADGE DE UNIÓN: solo aparece si esta mesa está unida a otra --}}
                    @if($table->mergeAsPrimary)
                        <div class="table-merge-badge">
                            <i class="fas fa-link"></i> Unida con {{ $table->mergeAsPrimary->secondaryTable->name }}
                        </div>
                    @elseif($table->mergeAsSecondary)
                        <div class="table-merge-badge">
                            <i class="fas fa-link"></i> Unida con {{ $table->mergeAsSecondary->primaryTable->name }}
                        </div>
                    @endif

                    {{-- TOP --}}
                    <div class="d-flex justify-content-between align-items-start">

                        <div class="table-title">
                            {{ $table->name }}
                        </div>

                        <div class="d-flex align-items-center" style="gap:6px;">

                            {{-- BOTÓN UNIR: solo si tiene permiso, la mesa tiene orden propia,
                                 no está ya unida, y no estamos en medio de otra unión --}}
                            @can('floor_map.merge_tables')
                                @if($table->activeOrder && !$table->mergeAsPrimary && !$table->mergeAsSecondary && !$mergingTableId)
                                    <button
                                        type="button"
                                        wire:click.stop="startMerge({{ $table->id }})"
                                        class="table-merge-btn"
                                        title="Unir mesa"
                                    >
                                        <i class="fas fa-link"></i>
                                    </button>
                                @endif
                            @endcan

                            <div class="table-status-circle"></div>

                        </div>

                    </div>

                    <div class="table-pax">
                        <i class="fas fa-user-friends"></i> {{ $table->capacity }} personas
                    </div>

                    {{-- CENTER --}}
                    <div class="table-center">

                        @switch($table->operationalStatus())

                            @case('available')
                                <div class="table-status-label available">
                                    <i class="fas fa-circle-check"></i> Disponible
                                </div>
                            @break

                            @case('occupied')
                                <div class="table-status-label occupied">
                                    <i class="fas fa-user-clock"></i> Ocupada
                                </div>
                            @break

                            @case('reserved')
                                <div class="table-status-label reserved">
                                    <i class="fas fa-bookmark"></i> Reservada
                                </div>
                            @break

                            @case('cleaning')
                                <div class="table-status-label cleaning">
                                    <i class="fas fa-broom"></i> Limpieza
                                </div>
                            @break

                            @case('pending_payment')
                                <div class="table-status-label payment">
                                    <i class="fas fa-money-bill-wave"></i> Pago
                                </div>
                            @break

                        @endswitch

                    </div>

                    {{-- FOOTER --}}
                    <div class="table-footer">

                        @if($table->waiter)
                            <div class="table-waiter">
                                <i class="fas fa-user"></i>
                                {{ \Illuminate\Support\Str::limit($table->waiter->name, 16) }}
                            </div>
                        @endif

                        @if($table->activeOrder)
                            <div class="table-order">
                                Orden #{{ $table->activeOrder->id }}
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">
                <div class="alert alert-light border">
                    No hay mesas registradas.
                </div>
            </div>

        @endforelse

    </div>

</div>

@push('css')

<style>

/* =========================================================
   FLOOR TABS
========================================================= */
.floor-tabs-wrapper {
    overflow-x: auto;
    width: 100%;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    margin-bottom: 4px;
}
.floor-tabs-wrapper::-webkit-scrollbar { display: none; }

.floor-tabs-scroll {
    display: inline-flex;
    gap: 10px;
    min-width: max-content;
    padding-bottom: 4px;
}

.floor-chip {
    display: inline-flex;
    align-items: center;
    border: 2px solid var(--brand-border, #f1d2bd);
    border-radius: 30px;
    padding: 9px 20px;
    background: var(--brand-light-alt, #fbe9e7);
    color: var(--brand-dark, #140a0a);
    font-weight: 700;
    font-size: .92rem;
    white-space: nowrap;
    transition: all .18s ease;
}

.floor-chip:hover {
    transform: translateY(-1px);
}

.floor-chip.active {
    color: #fff;
    box-shadow: 0 6px 14px rgba(142,0,0,.28);
}

/* =========================================================
   ACTIVE FLOOR BANNER
========================================================= */
.active-floor-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    background: color-mix(in srgb, var(--floor-color) 10%, #fff);
    border-left: 6px solid var(--floor-color);
    border-radius: 10px;
    padding: 12px 18px;
}

.active-floor-banner-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--floor-color);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.active-floor-banner-label {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #777;
    font-weight: 700;
}

.active-floor-banner-name {
    font-size: 1.1rem;
    font-weight: 800;
    color: #222;
}

.active-floor-banner-count {
    margin-left: auto;
    font-weight: 700;
    color: var(--brand-dark, #140a0a);
    font-size: .88rem;
    background: #fff;
    border: 1px solid var(--floor-color);
    border-radius: 20px;
    padding: 5px 14px;
    white-space: nowrap;
}

/* =========================================================
   STATUS LEGEND
========================================================= */
.status-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    background: var(--brand-light-alt, #fbe9e7);
    border: 1px solid var(--brand-border, #f1d2bd);
    border-radius: 10px;
    padding: 10px 16px;
}

.status-legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .78rem;
    font-weight: 600;
    color: #555;
}

.status-legend-item .dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    display: inline-block;
}

/* =========================================================
   MERGE MODE BANNER
========================================================= */
.merge-mode-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #e3f2fd;
    border: 1px solid #90caf9;
    color: #0d47a1;
    font-weight: 700;
    border-radius: 10px;
    padding: 10px 16px;
    font-size: .88rem;
}

/* =========================================================
   POS TABLE CARD
========================================================= */
.table-pos-card {
    background: #fff;
    border-radius: 20px;
    padding: 16px;
    min-height: 210px;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    transition: all .2s ease;
    box-shadow: 0 8px 18px rgba(142,0,0,.08);
}

.table-pos-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 28px rgba(142,0,0,.14);
}

.table-pos-card:active {
    transform: scale(.98);
}

/* Mesa origen de la unión (la que ya elegiste) */
.table-pos-card.merge-origin {
    outline: 3px solid #1565C0;
    outline-offset: 2px;
}

/* Mesas candidatas a recibir la unión: parpadeo suave para guiar el ojo */
.table-pos-card.merge-target-hint {
    animation: merge-pulse 1.2s infinite;
}

@keyframes merge-pulse {
    0%, 100% { box-shadow: 0 8px 18px rgba(142,0,0,.08); }
    50% { box-shadow: 0 0 0 4px rgba(21,101,192,.25); }
}

/* Franja superior = color de ESTADO (a la izquierda como acento fuerte) */
.status-available { border-top: 6px solid #43a047; }
.status-occupied   { border-top: 6px solid #c62828; }
.status-reserved   { border-top: 6px solid #ff9800; }
.status-cleaning   { border-top: 6px solid #039be5; }
.status-payment    { border-top: 6px solid #424242; }

/* Badge de piso: mismo color que la pestaña/banner activos (--floor-color) */
.table-floor-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .66rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #5a4a42;
    background: #fff;
    border: 1.5px solid var(--floor-color);
    border-radius: 20px;
    padding: 3px 10px;
    margin-bottom: 10px;
    margin-right: 6px;
}

.table-floor-badge i {
    color: var(--floor-color);
}

/* Badge de unión */
.table-merge-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .66rem;
    font-weight: 800;
    color: #fff;
    background: #37474f;
    border-radius: 20px;
    padding: 3px 10px;
    margin-bottom: 10px;
}

/* Botón unir mesa (icono cadena, junto al círculo de estado) */
.table-merge-btn {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: none;
    background: rgba(0,0,0,.06);
    color: #5a4a42;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .72rem;
    cursor: pointer;
    transition: all .15s ease;
    flex-shrink: 0;
}

.table-merge-btn:hover {
    background: var(--floor-color);
    color: #fff;
}

/* =========================================================
   HEADER
========================================================= */
.table-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #222;
}

.table-pax {
    color: #777;
    font-size: .82rem;
    margin-top: 2px;
    margin-bottom: 4px;
}

/* =========================================================
   STATUS CIRCLE
========================================================= */
.table-status-circle {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 4px;
}

.status-available .table-status-circle { background: #43a047; }
.status-occupied .table-status-circle   { background: #c62828; }
.status-reserved .table-status-circle   { background: #ff9800; }
.status-cleaning .table-status-circle   { background: #039be5; }
.status-payment .table-status-circle    { background: #424242; }

/* =========================================================
   CENTER STATUS
========================================================= */
.table-center {
    margin-top: 14px;
}

.table-status-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: .85rem;
    font-weight: 800;
}

.available { background: rgba(67,160,71,.14);  color: #2e7d32; }
.occupied  { background: rgba(198,40,40,.14);  color: #b71c1c; }
.reserved  { background: rgba(255,152,0,.14);  color: #ef6c00; }
.cleaning  { background: rgba(3,155,229,.14);  color: #0277bd; }
.payment   { background: rgba(66,66,66,.14);   color: #212121; }

/* =========================================================
   FOOTER
========================================================= */
.table-footer {
    position: absolute;
    left: 16px;
    right: 16px;
    bottom: 14px;
}

.table-waiter {
    color: #666;
    font-size: .8rem;
}

.table-order {
    margin-top: 4px;
    font-size: .8rem;
    color: var(--brand-primary, #c62828);
    font-weight: 700;
}

/* =========================================================
   MOBILE
========================================================= */
@media(max-width:768px){

    .table-pos-card { min-height: 190px; padding: 14px; }
    .table-title { font-size: 1rem; }
    .floor-chip { padding: 8px 14px; font-size: .82rem; }
    .table-status-label { width: 100%; justify-content: center; }
    .active-floor-banner-count { display: none; }
    .status-legend { gap: 10px; padding: 8px 12px; }
    .merge-mode-banner { font-size: .8rem; }
}

</style>

@endpush

@push('script')
<script>

document.addEventListener('livewire:init', () => {

    Livewire.on('show-error', (event) => {

        Swal.fire({
            icon: 'warning',
            title: event.title ?? 'Aviso',
            text: event.message,
            confirmButtonText: 'Entendido'
        });

    });

    Livewire.on('show-success', (event) => {

        Swal.fire({
            icon: 'success',
            title: '¡Listo!',
            text: event.message,
            confirmButtonText: 'Entendido',
            timer: 2000,
            showConfirmButton: false
        });

    });

});

</script>
@endpush