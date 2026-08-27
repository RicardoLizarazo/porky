@php
    // Misma paleta y misma lógica de color/ícono por piso que en el resto del sistema,
    // basada en el ID del piso, para que se vea igual en mapa de mesas, caja y aquí.
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

    $fColor = $floorPalette[((int) ($order->floor?->id ?? 0)) % count($floorPalette)];
    $fIcon = $floorIcons[((int) ($order->floor?->id ?? 0)) % count($floorIcons)];

    $cartCount = collect($cart)->sum('quantity');

    $isVitrina = ($order->floor?->name ?? '') === 'Vitrina';
    $vitrinaStation = null;

    if ($isVitrina && preg_match('/(\d+)$/', $order->diningTable?->name ?? '', $m)) {
        $vitrinaStation = $m[1];
    }
@endphp

<div class="container-fluid px-2 px-md-3 pos-mesa-wrapper" @if($isVitrina) wire:poll.5s="refreshOrderStatus" @endif>

    {{-- HEADER FIJO --}}
    <div class="pos-header sticky-top mb-3">

        <div class="pos-header-top">

            <div class="pos-floor-badge" style="--floor-color: {{ $fColor }};">
                <i class="fas {{ $fIcon }}"></i>
                {{ $order->floor?->name ?? 'Sin piso' }}
            </div>

            <div class="pos-mesa-name">
                <i class="fas fa-chair"></i>
                {{ $order->diningTable?->name ?? 'Mesa' }}
            </div>

        </div>

        <div class="pos-header-bottom">

            <div class="pos-waiter">
                <i class="fas fa-user-tie"></i>
                <span>{{ auth()->user()->name }}</span>
            </div>

            <div class="pos-total">
                <span class="pos-total-label">Total</span>
                <span class="pos-total-amount">
                    ${{ number_format($this->total, 0, ',', '.') }}
                </span>
            </div>

        </div>

        @if($isVitrina && $vitrinaStation)
            @if($order->is_paid)
                <a
                    href="{{ route('pos.vitrina', $vitrinaStation) }}"
                    class="pos-new-client-btn"
                >
                    <i class="fas fa-user-plus"></i>
                    Nuevo cliente
                </a>
            @else
                <button
                    type="button"
                    class="pos-new-client-btn pos-new-client-btn-locked"
                    onclick="Swal.fire({
                        icon: 'info',
                        title: 'Pedido pendiente de cobro',
                        text: 'Esta estacion queda ocupada hasta que Caja Vitrina cobre este pedido.'
                    })"
                >
                    <i class="fas fa-lock"></i>
                    Esperando cobro
                </button>
            @endif
        @endif

    </div>

    {{-- SEARCH --}}
    <div class="mb-3">
        <input
            type="text"
            wire:model.live="search"
            class="form-control form-control-lg pos-search"
            placeholder="Buscar producto..."
        >
    </div>

    {{-- CATEGORIES --}}
    <div class="pos-categories-wrapper mb-3">
        <div class="pos-categories-scroll">
            <button
                wire:click="$set('category_id', null)"
                class="pos-cat-chip {{ !$category_id ? 'active' : '' }}"
            >
                Todos
            </button>

            @foreach($categories as $category)
                <button
                    wire:click="filterCategory({{ $category->id }})"
                    class="pos-cat-chip {{ $category_id == $category->id ? 'active' : '' }}"
                >
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="row">

        {{-- PRODUCTS --}}
        <div class="col-lg-8">
            <div class="row">
                @foreach($products as $product)
                    <div class="col-6 col-md-6 mb-3">
                        <div class="product-card-pro shadow-sm">

                            <div class="product-img-wrapper">
                                <img
                                    src="{{ $product->image
                                        ? asset('storage/'.$product->image)
                                        : asset('images/no-image.png') }}"
                                    class="product-img"
                                >
                            </div>

                            <div class="product-info">

                                <h6 class="product-name">
                                    {{ $product->name }}
                                </h6>

                                <small class="product-category">
                                    {{ $product->category?->name }}
                                </small>

                                <div class="product-footer">
                                    <span class="product-price">
                                        ${{ number_format($product->price, 0, ',', '.') }}
                                    </span>

                                    <button
                                        wire:click="addProduct({{ $product->id }})"
                                        class="btn-add-product"
                                    >
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>

                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CART: SIDEBAR FIJO (tablet horizontal / desktop) --}}
        <div class="col-lg-4 d-none d-lg-block">
            <div class="card sticky-top pos-cart-card">
                <div class="card-header">
                    <i class="fas fa-utensils mr-1"></i> Pedido
                </div>
                <div class="card-body pos-cart-body">
                    @include('restaurante.pos.cart-items')
                </div>
                <div class="card-footer">
                    @include('restaurante.pos.cart-footer')
                </div>
            </div>
        </div>

    </div>

    {{-- BARRA FLOTANTE: solo en pantallas angostas (celular / tablet vertical) --}}
    <button
        type="button"
        class="pos-cart-fab d-lg-none"
        onclick="document.getElementById('pos-cart-drawer').classList.add('open')"
    >
        <span class="pos-cart-fab-count">{{ $cartCount }}</span>
        <span class="pos-cart-fab-label">Ver pedido</span>
        <span class="pos-cart-fab-total">
            ${{ number_format($this->total, 0, ',', '.') }}
        </span>
        <i class="fas fa-chevron-up"></i>
    </button>

    {{-- PANEL DESLIZANTE DEL CARRITO: solo en pantallas angostas --}}
    <div id="pos-cart-drawer" class="pos-cart-drawer d-lg-none">
        <div class="pos-cart-drawer-backdrop" onclick="document.getElementById('pos-cart-drawer').classList.remove('open')"></div>

        <div class="pos-cart-drawer-panel">

            <div class="pos-cart-drawer-handle"></div>

            <div class="pos-cart-drawer-header">
                <span><i class="fas fa-utensils mr-1"></i> Pedido</span>
                <button
                    type="button"
                    class="pos-cart-drawer-close"
                    onclick="document.getElementById('pos-cart-drawer').classList.remove('open')"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="pos-cart-drawer-body">
                @include('restaurante.pos.cart-items')
            </div>

            <div class="pos-cart-drawer-footer">
                @include('restaurante.pos.cart-footer')
            </div>

        </div>
    </div>

    {{-- MODAL DE OPCIONES --}}
    @include('restaurante.pos.options-modal')

</div>

@push('css')

@if($isVitrina)
<style>
/* =========================================================
   MODO VITRINA: sin menú, pantalla completa para el picador
========================================================= */
.main-sidebar,
.main-header,
.control-sidebar {
    display: none !important;
}

.content-wrapper,
.wrapper {
    margin-left: 0 !important;
}

.content-wrapper {
    padding-top: 0 !important;
}
</style>
@endif

<style>

/* =========================================================
   HEADER FIJO
========================================================= */
.pos-header {
    background: #fff;
    border-radius: 14px;
    padding: 12px 16px;
    box-shadow: 0 6px 16px rgba(142,0,0,.08);
    z-index: 1020;
}

.pos-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
}

.pos-floor-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #5a4a42;
    background: #fff;
    border: 1.5px solid var(--floor-color);
    border-radius: 20px;
    padding: 4px 10px;
    flex-shrink: 0;
}

.pos-floor-badge i {
    color: var(--floor-color);
}

.pos-mesa-name {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--brand-dark, #140a0a);
    display: flex;
    align-items: center;
    gap: 8px;
    text-align: right;
}

.pos-mesa-name i {
    color: var(--brand-primary);
    font-size: 1.05rem;
}

.pos-header-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px dashed var(--brand-border, #f1d2bd);
    padding-top: 8px;
}

.pos-waiter {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .95rem;
    font-weight: 700;
    color: #444;
}

.pos-waiter i {
    color: var(--brand-primary);
}

.pos-total {
    display: flex;
    align-items: baseline;
    gap: 8px;
}

.pos-total-label {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #999;
    font-weight: 700;
}

.pos-total-amount {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--brand-primary-dark, #8e0000);
}

.pos-new-client-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
    padding: 10px;
    border-radius: 10px;
    background: var(--brand-light-alt, #fbe9e7);
    color: var(--brand-primary-dark, #8e0000);
    font-weight: 800;
    font-size: .88rem;
    border: none;
    width: 100%;
}

.pos-new-client-btn:hover {
    background: var(--brand-border, #f1d2bd);
    color: var(--brand-primary-dark, #8e0000);
}

.pos-new-client-btn-locked {
    background: #f1f1f1;
    color: #999;
    border: none;
    cursor: not-allowed;
}

.pos-new-client-btn-locked:hover {
    background: #f1f1f1;
    color: #999;
}

/* =========================================================
   BUSCADOR
========================================================= */
.pos-search {
    border-radius: 12px;
    border: 1.5px solid var(--brand-border, #f1d2bd);
}

/* =========================================================
   CATEGORÍAS
========================================================= */
.pos-categories-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.pos-categories-wrapper::-webkit-scrollbar { display: none; }

.pos-categories-scroll {
    display: inline-flex;
    gap: 8px;
    min-width: max-content;
    padding-bottom: 2px;
}

.pos-cat-chip {
    border: 1.5px solid var(--brand-border, #f1d2bd);
    background: #fff;
    color: var(--brand-dark, #140a0a);
    border-radius: 20px;
    padding: 9px 18px;
    font-weight: 700;
    font-size: .85rem;
    white-space: nowrap;
    transition: all .15s ease;
}

.pos-cat-chip.active {
    background: linear-gradient(90deg, var(--brand-secondary), var(--brand-primary));
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 12px rgba(198,40,40,.3);
}

/* =========================================================
   TARJETA DE PRODUCTO (optimizada para dedo/touch)
========================================================= */
.product-card-pro {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-img-wrapper {
    width: 100%;
    aspect-ratio: 4 / 3;
    background: #f5f5f5;
    overflow: hidden;
}

.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info {
    padding: 10px 12px 12px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-weight: 700;
    font-size: .88rem;
    margin-bottom: 2px;
    line-height: 1.25;
}

.product-category {
    color: #999;
    font-size: .72rem;
    margin-bottom: auto;
}

.product-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 10px;
}

.product-price {
    font-weight: 800;
    color: var(--brand-primary-dark, #8e0000);
    font-size: .92rem;
}

.btn-add-product {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, var(--brand-secondary), var(--brand-primary));
    color: #fff;
    font-size: .95rem;
    box-shadow: 0 4px 10px rgba(198,40,40,.3);
}

.btn-add-product:active {
    transform: scale(.92);
}

/* =========================================================
   CARRITO — SIDEBAR (desktop / tablet horizontal)
========================================================= */
.pos-cart-card {
    border-radius: 14px;
    overflow: hidden;
}

.pos-cart-body {
    max-height: calc(100vh - 340px);
    overflow-y: auto;
}

/* =========================================================
   BARRA FLOTANTE (móvil / tablet vertical)
========================================================= */
.pos-cart-fab {
    position: fixed;
    left: 12px;
    right: 12px;
    bottom: 12px;
    z-index: 1030;
    border: none;
    border-radius: 16px;
    padding: 14px 18px;
    background: linear-gradient(90deg, var(--brand-dark), var(--brand-primary));
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 24px rgba(0,0,0,.3);
    font-weight: 700;
}

.pos-cart-fab-count {
    background: #fff;
    color: var(--brand-primary);
    border-radius: 50%;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    font-weight: 800;
    flex-shrink: 0;
}

.pos-cart-fab-label {
    flex-grow: 1;
    text-align: left;
    font-size: .9rem;
}

.pos-cart-fab-total {
    font-size: 1rem;
    font-weight: 800;
}

/* =========================================================
   PANEL DESLIZANTE (drawer del carrito en móvil)
========================================================= */
.pos-cart-drawer {
    position: fixed;
    inset: 0;
    z-index: 1050;
    pointer-events: none;
}

.pos-cart-drawer-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(20,10,10,.5);
    opacity: 0;
    transition: opacity .25s ease;
}

.pos-cart-drawer-panel {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    max-height: 88vh;
    background: #fff;
    border-radius: 20px 20px 0 0;
    display: flex;
    flex-direction: column;
    transform: translateY(100%);
    transition: transform .28s ease;
    box-shadow: 0 -10px 30px rgba(0,0,0,.25);
}

.pos-cart-drawer.open {
    pointer-events: auto;
}

.pos-cart-drawer.open .pos-cart-drawer-backdrop {
    opacity: 1;
}

.pos-cart-drawer.open .pos-cart-drawer-panel {
    transform: translateY(0);
}

.pos-cart-drawer-handle {
    width: 42px;
    height: 4px;
    background: var(--brand-border, #f1d2bd);
    border-radius: 4px;
    margin: 10px auto 4px;
    flex-shrink: 0;
}

.pos-cart-drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 18px 12px;
    font-weight: 800;
    border-bottom: 1px solid var(--brand-border, #f1d2bd);
    flex-shrink: 0;
}

.pos-cart-drawer-close {
    border: none;
    background: var(--brand-light-alt, #fbe9e7);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    color: var(--brand-dark, #140a0a);
}

.pos-cart-drawer-body {
    padding: 14px 18px;
    overflow-y: auto;
    flex-grow: 1;
}

.pos-cart-drawer-footer {
    padding: 14px 18px 20px;
    border-top: 1px solid var(--brand-border, #f1d2bd);
    flex-shrink: 0;
}

/* =========================================================
   ESPACIO RESERVADO PARA LA BARRA FLOTANTE (móvil/tablet vertical)
========================================================= */
@media (max-width: 991.98px) {
    .pos-mesa-wrapper {
        padding-bottom: calc(90px + env(safe-area-inset-bottom, 0px));
    }
}

/* =========================================================
   MOBILE FINE-TUNING
========================================================= */
@media(max-width: 768px){
    .pos-mesa-name { font-size: 1.1rem; }
    .pos-total-amount { font-size: 1.25rem; }
    .product-name { font-size: .82rem; }
}

</style>
@endpush

@push('script')
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('success', () => {
            Swal.fire({
                icon: 'success',
                title: 'Pedido enviado a cocina.',
                showConfirmButton: false,
                timer: 1500
            });

            const drawer = document.getElementById('pos-cart-drawer');
            if (drawer) {
                drawer.classList.remove('open');
            }
        });

        Livewire.on('swal', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                icon: data.icon || 'info',
                title: data.title || '',
            });
        });

        Livewire.on('show-error', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                icon: 'error',
                title: 'No autorizado',
                text: data.message || 'Ocurrio un error.',
                confirmButtonText: 'Entendido'
            });
        });
    });
</script>
@endpush