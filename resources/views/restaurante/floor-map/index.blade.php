<div class="container-fluid px-2 px-md-3" wire:poll.5s>

    {{-- PISOS --}}
    <div class="floor-tabs-wrapper mb-3">

        <div class="floor-tabs-scroll">

            @foreach($floors as $floor)

                <button
                    wire:click="$set('selectedFloor', {{ $floor->id }})"

                    class="floor-tab-btn"

                    @class([
                        'active' => $selectedFloor == $floor->id
                    ])
                >
                    {{ $floor->name }}
                </button>

            @endforeach

        </div>

    </div>

    {{-- GRID --}}
    <div class="row">

        @forelse($tables as $table)

            <div class="col-6 col-lg-4 mb-3">

                <div
                    wire:click="openTable({{ $table->id }})"
                    class="table-pos-card"

                    @class([

                        'status-available' =>
                            $table->operationalStatus() === 'available',

                        'status-occupied' =>
                            $table->operationalStatus() === 'occupied',

                        'status-reserved' =>
                            $table->operationalStatus() === 'reserved',

                        'status-cleaning' =>
                            $table->operationalStatus() === 'cleaning',

                        'status-payment' =>
                            $table->operationalStatus() === 'pending_payment',
                    ])
                >

                    {{-- TOP --}}
                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="table-title">
                                {{ $table->name }}
                            </div>

                            <div class="table-pax">
                                👥 {{ $table->capacity }} personas
                            </div>

                        </div>

                        <div class="table-status-circle"></div>

                    </div>

                    {{-- CENTER --}}
                    <div class="table-center">

                        @switch($table->operationalStatus())

                            @case('available')

                                <div class="table-status-label available">

                                    Disponible

                                </div>

                            @break

                            @case('occupied')

                                <div class="table-status-label occupied">

                                    Ocupada

                                </div>

                            @break

                            @case('reserved')

                                <div class="table-status-label reserved">

                                    Reservada

                                </div>

                            @break

                            @case('cleaning')

                                <div class="table-status-label cleaning">

                                    Limpieza

                                </div>

                            @break

                            @case('pending_payment')

                                <div class="table-status-label payment">

                                    Pago

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

    margin-bottom: 15px;
}

.floor-tabs-wrapper::-webkit-scrollbar {

    display: none;
}

.floor-tabs-scroll {

    display: inline-flex;

    gap: 10px;

    min-width: max-content;

    padding-bottom: 4px;
}

.floor-tab-btn {

    border: none;

    background: #fff;

    color: #444;

    padding: 10px 18px;

    border-radius: 16px;

    font-weight: 600;

    white-space: nowrap;

    flex-shrink: 0;

    box-shadow: 0 4px 12px rgba(0,0,0,.06);

    transition: all .18s ease;
}

.floor-tab-btn.active {

    background: linear-gradient(
        135deg,
        var(--brand-primary),
        var(--brand-secondary)
    );

    color: #fff;

    box-shadow: 0 6px 16px rgba(198,40,40,.3);
}

/* =========================================================
   POS TABLE CARD
========================================================= */

.table-pos-card {

    background: #fff;

    border-radius: 22px;

    padding: 18px;

    min-height: 190px;

    position: relative;

    overflow: hidden;

    cursor: pointer;

    transition: all .2s ease;

    box-shadow: 0 8px 18px rgba(0,0,0,.08);
}

.table-pos-card:hover {

    transform: translateY(-3px);

    box-shadow: 0 14px 28px rgba(0,0,0,.12);
}

.table-pos-card:active {

    transform: scale(.98);
}

/* =========================================================
   STATUS TOP BORDER
========================================================= */

.status-available {

    border-top: 6px solid #43a047;
}

.status-occupied {

    border-top: 6px solid #c62828;
}

.status-reserved {

    border-top: 6px solid #ff9800;
}

.status-cleaning {

    border-top: 6px solid #039be5;
}

.status-payment {

    border-top: 6px solid #424242;
}

/* =========================================================
   HEADER
========================================================= */

.table-title {

    font-size: 1.2rem;

    font-weight: 700;

    color: #222;
}

.table-pax {

    color: #777;

    font-size: .85rem;

    margin-top: 2px;
}

/* =========================================================
   STATUS CIRCLE
========================================================= */

.table-status-circle {

    width: 16px;

    height: 16px;

    border-radius: 50%;
}

.status-available .table-status-circle {

    background: #43a047;
}

.status-occupied .table-status-circle {

    background: #c62828;
}

.status-reserved .table-status-circle {

    background: #ff9800;
}

.status-cleaning .table-status-circle {

    background: #039be5;
}

.status-payment .table-status-circle {

    background: #424242;
}

/* =========================================================
   CENTER STATUS
========================================================= */

.table-center {

    margin-top: 22px;
}

.table-status-label {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 8px 16px;

    border-radius: 30px;

    font-size: .82rem;

    font-weight: 700;
}

.available {

    background: rgba(67,160,71,.12);

    color: #2e7d32;
}

.occupied {

    background: rgba(198,40,40,.12);

    color: #b71c1c;
}

.reserved {

    background: rgba(255,152,0,.12);

    color: #ef6c00;
}

.cleaning {

    background: rgba(3,155,229,.12);

    color: #0277bd;
}

.payment {

    background: rgba(66,66,66,.12);

    color: #212121;
}

/* =========================================================
   FOOTER
========================================================= */

.table-footer {

    position: absolute;

    left: 18px;

    right: 18px;

    bottom: 18px;
}

.table-waiter {

    color: #666;

    font-size: .82rem;
}

.table-order {

    margin-top: 4px;

    font-size: .82rem;

    color: var(--brand-primary);

    font-weight: 700;
}

/* =========================================================
   MOBILE
========================================================= */

@media(max-width:768px){

    .table-pos-card {

        min-height: 165px;

        padding: 14px;
    }

    .table-title {

        font-size: 1rem;
    }

    .floor-tab-btn {

        padding: 8px 14px;

        font-size: .82rem;
    }

    .table-status-label {

        width: 100%;

        justify-content: center;
    }
}

</style>

@endpush

@push('script')
<script>

document.addEventListener('livewire:init', () => {

    Livewire.on('show-error', (event) => {

        Swal.fire({
            icon: 'warning',
            title: 'Mesa ocupada',
            text: event.message,
            confirmButtonText: 'Entendido'
        });

    });

});

</script>
@endpush