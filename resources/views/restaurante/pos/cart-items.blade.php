@forelse($cart as $item)

    <div class="pos-cart-item border-bottom pb-3 mb-3">

        <div class="d-flex justify-content-between">

            <div class="flex-grow-1 pr-2">

                <strong class="d-block">
                    {{ $item['product']['name'] }}
                </strong>

                <div class="small text-muted">
                    x{{ $item['quantity'] }}
                </div>

                @if($item['product']['allow_manual_price'])

                    <div class="mt-2">

                        <small class="text-muted d-block mb-1">
                            Valor por porción
                        </small>

                        <input
                            type="number"
                            min="0"
                            step="500"
                            value="{{ $item['manual_price'] ?: $item['price'] }}"
                            wire:change="updateManualPrice(
                                {{ $item['id'] }},
                                $event.target.value
                            )"
                            class="form-control form-control-sm pos-manual-price"
                        >

                    </div>

                @endif

                @if($item['manual_price'])
                    <div class="small text-primary mt-1">
                        💲 Ajustado:
                        ${{ number_format($item['manual_price'], 0, ',', '.') }}
                    </div>
                @endif

                @php
                    preg_match('/EMPAQUE=(\d+)/', $item['comment'] ?? '', $matches);
                    $packagingQty = (int) ($matches[1] ?? 0);
                @endphp

                @if($packagingQty > 0)
                    <div class="small text-warning mt-1">
                        🥡 {{ $packagingQty }}
                        x
                        ${{ number_format($item['product']['packaging_cost'] ?? 0, 0, ',', '.') }}
                    </div>
                @endif

            </div>

            <div class="text-right flex-shrink-0">

                <div class="font-weight-bold mb-2">
                    ${{ number_format($item['subtotal'], 0, ',', '.') }}
                </div>

                {{-- CONTROLES DE CANTIDAD (táctiles) --}}
                <div class="pos-qty-controls">
                    <button
                        wire:click="removeProduct({{ $item['id'] }})"
                        class="btn-qty btn-qty-minus"
                    >
                        −
                    </button>

                    <span class="pos-qty-value">
                        {{ $item['quantity'] }}
                    </span>

                    <button
                        wire:click="addProduct({{ $item['product_id'] }})"
                        class="btn-qty btn-qty-plus"
                    >
                        +
                    </button>
                </div>

                <button
                    wire:click="deleteDetail({{ $item['id'] }})"
                    class="btn-qty-delete mt-2"
                >
                    <i class="fas fa-trash-alt"></i> Quitar
                </button>

                {{-- EMPAQUE --}}
                @if(($item['product']['packaging_cost'] ?? 0) > 0)

                    <div class="mt-3">
                        <small class="text-muted d-block mb-1">
                            Para llevar
                        </small>
                        <div class="pos-qty-controls pos-qty-controls-packaging">
                            <button
                                wire:click="decreasePackaging({{ $item['id'] }})"
                                class="btn-qty btn-qty-minus"
                            >
                                −
                            </button>

                            <span class="pos-qty-value">
                                🥡 {{ $packagingQty }}
                            </span>

                            <button
                                wire:click="increasePackaging({{ $item['id'] }})"
                                class="btn-qty btn-qty-plus"
                            >
                                +
                            </button>
                        </div>
                    </div>

                @endif

            </div>

        </div>

    </div>

@empty
    <div class="text-muted text-center py-4">
        <i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>
        No hay productos
    </div>
@endforelse

@push('css')
<style>
.pos-qty-controls {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--brand-light-alt, #fbe9e7);
    border-radius: 30px;
    padding: 4px;
}

.btn-qty {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: none;
    background: #fff;
    color: var(--brand-primary);
    font-size: 1.1rem;
    font-weight: 800;
    line-height: 1;
    box-shadow: 0 2px 6px rgba(0,0,0,.1);
}

.btn-qty-plus {
    background: linear-gradient(135deg, var(--brand-secondary), var(--brand-primary));
    color: #fff;
}

.pos-qty-value {
    min-width: 26px;
    text-align: center;
    font-weight: 800;
    font-size: .95rem;
}

.btn-qty-delete {
    border: none;
    background: transparent;
    color: #b71c1c;
    font-size: .78rem;
    font-weight: 700;
    padding: 4px 6px;
}

.pos-manual-price {
    width: 130px;
    border-radius: 8px;
}
</style>
@endpush