@php
    // Misma paleta y misma lógica de color/ícono por piso que en el mapa de mesas,
    // basada en el ID del piso, para que un piso se vea IDÉNTICO en ambas pantallas.
    $floorPalette = [
        'var(--brand-success)', // marrón cálido
        '#8d6e63',              // taupe / mocha
        '#a9720b',              // dorado oscuro
        '#4e342e',              // chocolate
        '#795548',              // marrón medio
        '#6d5300',              // mostaza oscuro
        '#5d4037',              // marrón profundo
        '#33261a',              // marrón casi negro
    ];

    $floorIcons = ['fa-building', 'fa-door-open', 'fa-store', 'fa-umbrella-beach', 'fa-glass-martini-alt', 'fa-warehouse', 'fa-tree', 'fa-layer-group'];

    $floorColor = fn ($floorId) => $floorPalette[((int) $floorId) % count($floorPalette)];
    $floorIcon = fn ($floorId) => $floorIcons[((int) $floorId) % count($floorIcons)];

    $pendingTotal = $orders->sum('total');
@endphp

<div>
    @include('livewire.cash.payment-modal')

    {{-- RESUMEN --}}
    <div class="cobro-summary mb-3">
        <div class="cobro-summary-item">
            <div class="cobro-summary-icon"><i class="fas fa-receipt"></i></div>
            <div>
                <div class="cobro-summary-label">Cuentas pendientes</div>
                <div class="cobro-summary-value">{{ $orders->count() }}</div>
            </div>
        </div>
        <div class="cobro-summary-item">
            <div class="cobro-summary-icon"><i class="fas fa-sack-dollar"></i></div>
            <div>
                <div class="cobro-summary-label">Total por cobrar</div>
                <div class="cobro-summary-value">${{ number_format($pendingTotal, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="row">

        @forelse($orders as $order)

            @php
                $fColor = $floorColor($order->floor?->id);
                $fIcon = $floorIcon($order->floor?->id);
            @endphp

            <div class="col-md-4 mb-3">
                <div class="cobro-card" style="--floor-color: {{ $fColor }};">

                    {{-- HEADER: piso (acento) + mesa (protagonista) --}}
                    <div class="cobro-card-header">

                        <div class="cobro-floor-badge">
                            <i class="fas {{ $fIcon }}"></i>
                            {{ $order->floor?->name ?? 'Sin piso' }}
                        </div>

                        <div class="cobro-table-name">
                            <i class="fas fa-chair"></i>
                            Mesa {{ $order->diningTable?->name }}
                        </div>

                        <div class="cobro-order-number">
                            Orden #{{ $order->id }}
                        </div>

                    </div>

                    {{-- BODY --}}
                    <div class="cobro-card-body">

                        <div class="cobro-waiter">
                            <i class="fas fa-user"></i>
                            {{ $order->user?->name ?? 'Sin mesero' }}
                        </div>

                        <div class="cobro-total">
                            <span class="cobro-total-label">Total a pagar</span>
                            <span class="cobro-total-amount">
                                ${{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="cobro-card-footer">
                        <a
                            href="{{ route('orders.tableTicket', $order->id) }}"
                            target="_blank"
                            class="btn-imprimir"
                            title="Imprimir ticket"
                        >
                            <i class="fas fa-receipt"></i>
                        </a>

                        <button
                            class="btn-cobrar"
                            wire:click="openPaymentModal({{ $order->id }})"
                        >
                            <i class="fas fa-cash-register"></i>
                            Cobrar
                        </button>
                    </div>

                </div>
            </div>

        @empty

            <div class="col-12">
                <div class="alert alert-success">
                    No hay mesas pendientes.
                </div>
            </div>

        @endforelse

    </div>
</div>

@push('css')
<style>

/* =========================================================
   RESUMEN SUPERIOR
========================================================= */
.cobro-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
}

.cobro-summary-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fff;
    border: 1px solid var(--brand-border, #f1d2bd);
    border-radius: 12px;
    padding: 12px 20px;
    box-shadow: 0 6px 16px rgba(142,0,0,.06);
    flex: 1 1 220px;
}

.cobro-summary-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--brand-secondary), var(--brand-primary));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.cobro-summary-label {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #888;
    font-weight: 700;
}

.cobro-summary-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--brand-dark, #140a0a);
}

/* =========================================================
   TARJETA DE COBRO
========================================================= */
.cobro-card {
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 18px rgba(142,0,0,.08);
    transition: all .2s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.cobro-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 28px rgba(142,0,0,.14);
}

/* ---- HEADER ---- */
.cobro-card-header {
    padding: 16px 18px 14px;
    border-bottom: 1px dashed var(--brand-border, #f1d2bd);
    position: relative;
}

.cobro-floor-badge {
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
}

.cobro-floor-badge i {
    color: var(--floor-color);
}

.cobro-table-name {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--brand-dark, #140a0a);
    display: flex;
    align-items: center;
    gap: 8px;
}

.cobro-table-name i {
    color: var(--brand-primary);
    font-size: 1rem;
}

.cobro-order-number {
    font-size: .74rem;
    color: #999;
    font-weight: 600;
    margin-top: 4px;
}

/* ---- BODY ---- */
.cobro-card-body {
    padding: 16px 18px;
    flex-grow: 1;
}

.cobro-waiter {
    font-size: .85rem;
    color: #666;
    font-weight: 600;
    margin-bottom: 14px;
}

.cobro-waiter i {
    color: var(--floor-color);
    margin-right: 6px;
    width: 14px;
}

.cobro-total {
    display: flex;
    flex-direction: column;
    background: var(--brand-light-alt, #fbe9e7);
    border-radius: 12px;
    padding: 12px 16px;
}

.cobro-total-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #8a5a52;
    font-weight: 700;
}

.cobro-total-amount {
    font-size: 1.7rem;
    font-weight: 800;
    color: var(--brand-primary-dark, #8e0000);
    line-height: 1.2;
}

/* ---- FOOTER ---- */
.cobro-card-footer {
    padding: 14px 18px 18px;
    display: flex;
    gap: 10px;
}

.btn-imprimir {
    flex-shrink: 0;
    width: 46px;
    height: 46px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--brand-dark, #140a0a);
    color: #fff;
    font-size: 1rem;
    transition: all .18s ease;
}

.btn-imprimir:hover {
    filter: brightness(1.3);
    color: #fff;
}

.btn-cobrar {
    flex: 1;
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 800;
    font-size: .92rem;
    color: #fff;
    background: linear-gradient(90deg, var(--brand-secondary), var(--brand-primary));
    box-shadow: 0 6px 14px rgba(198,40,40,.3);
    transition: all .18s ease;
}

.btn-cobrar:hover {
    filter: brightness(1.06);
    transform: translateY(-1px);
}

.btn-cobrar:active {
    transform: scale(.98);
}

/* =========================================================
   MOBILE
========================================================= */
@media(max-width:768px){
    .cobro-summary-item { padding: 10px 16px; }
    .cobro-table-name { font-size: 1.1rem; }
    .cobro-total-amount { font-size: 1.4rem; }
}

</style>
@endpush

@push('script')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-payment-modal', () => {
                $('#paymentModal').modal('show');
            });
            Livewire.on('close-payment-modal', () => {
                $('#paymentModal').modal('hide');
            });
        });
    </script>
@endpush