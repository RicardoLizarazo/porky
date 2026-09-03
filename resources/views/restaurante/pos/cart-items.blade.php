@forelse($cart as $item)

    @php
        $sentToKitchen = in_array($item['id'], $sentToKitchenIds ?? []);

        preg_match('/EMPAQUE=(\d+)/', $item['comment'] ?? '', $matches);
        $packagingQty = (int) ($matches[1] ?? 0);

        // Nota libre del mesero para cocina, guardada como NOTA=... dentro del mismo
        // campo comment (mismo patrón pipe-separated que EMPAQUE=N).
        preg_match('/NOTA=(.*)$/s', $item['comment'] ?? '', $noteMatches);
        $kitchenNote = trim($noteMatches[1] ?? '');

        // Todo lo que no sea el token de empaque ni el de nota son las opciones elegidas
        $optionsText = trim(preg_replace('/\s*\|?\s*EMPAQUE=\d+/', '', $item['comment'] ?? ''));
        $optionsText = trim(preg_replace('/\s*\|?\s*NOTA=.*$/s', '', $optionsText));
    @endphp

    <div class="pos-cart-item border-bottom pb-3 mb-3 {{ $sentToKitchen ? 'pos-cart-item-sent' : '' }}">

        <div class="d-flex justify-content-between">

            <div class="flex-grow-1 pr-2">

                <div class="d-flex align-items-center flex-wrap">
                    <strong class="d-block mr-2">
                        {{ $item['product']['name'] }}
                    </strong>

                    @if($sentToKitchen)
                        <span class="badge-sent-kitchen">
                            <i class="fas fa-check-circle"></i>
                            Enviado a cocina
                        </span>
                    @endif
                </div>

                <div class="small text-muted">
                    x{{ $item['quantity'] }}
                </div>

                {{-- OPCIONES ELEGIDAS --}}
                @if($optionsText)
                    <div class="small pos-item-options mt-1">
                        <i class="fas fa-sliders-h mr-1"></i>
                        {{ $optionsText }}
                    </div>
                @endif

                {{-- NOTA PARA COCINA (texto libre del mesero) --}}
                <div class="mt-2">
                    <small class="text-muted d-block mb-1">
                        <i class="fas fa-comment-dots mr-1"></i>
                        Nota para cocina
                    </small>

                    <input
                        type="text"
                        maxlength="120"
                        class="form-control form-control-sm pos-kitchen-note"
                        value="{{ $kitchenNote }}"
                        placeholder="Ej: sin cebolla, extra picante..."
                        wire:change="updateItemNote({{ $item['id'] }}, $event.target.value)"
                    >
                </div>

                @if($item['product']['allow_manual_price'])
                    <div class="mt-2">
                        <small class="text-muted d-block mb-1">
                            Valor por porción
                        </small>

                            <input 
                                type="number" 
                                inputmode="numeric" 
                                pattern="[0-9]*" 
                                min="0" 
                                step="1" 
                                class="form-control form-control-sm pos-manual-price" 
                                value="{{ $item['manual_price'] ?: $item['price'] }}" 
                                wire:change="updateManualPrice({{ $item['id'] }}, $event.target.value)"
                            >
                    </div>
                @endif

                @if($item['manual_price'])
                    <div class="small text-primary mt-1">
                        <i class="fas fa-tag mr-1"></i>
                        Ajustado:
                        ${{ number_format($item['manual_price'], 0, ',', '.') }}
                    </div>
                @endif

                @if($packagingQty > 0)
                    <div class="small text-warning mt-1">
                        <i class="fas fa-box mr-1"></i>
                        {{ $packagingQty }}
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
                        @if($sentToKitchen) disabled title="Ya enviado a cocina, usa Quitar para autorizar" @endif
                    >
                        −
                    </button>

                    <span class="pos-qty-value">
                        {{ $item['quantity'] }}
                    </span>

                    {{-- Repite ESTA linea con sus mismas opciones, sin volver a preguntar --}}
                    <button
                        wire:click="increaseDetail({{ $item['id'] }})"
                        class="btn-qty btn-qty-plus"
                    >
                        +
                    </button>
                </div>

                {{-- QUITAR: directo si no se ha enviado, con autorización si ya se envió --}}
                @if($sentToKitchen)
                    <button
                        type="button"
                        onclick="confirmRemoveWithAuth({{ $item['id'] }})"
                        class="btn-qty-delete btn-qty-delete-locked mt-2"
                    >
                        <i class="fas fa-lock"></i> Quitar
                    </button>
                @else
                    <button
                        wire:click="deleteDetail({{ $item['id'] }})"
                        class="btn-qty-delete mt-2"
                    >
                        <i class="fas fa-trash-alt"></i> Quitar
                    </button>
                @endif

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
                                <i class="fas fa-box"></i> {{ $packagingQty }}
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

.btn-qty[disabled] {
    opacity: .35;
    box-shadow: none;
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

.btn-qty-delete-locked {
    color: #8e6a00;
}

.pos-manual-price {
    width: 130px;
    border-radius: 8px;
}

.pos-kitchen-note {
    max-width: 260px;
    border-radius: 8px;
}

/* ---- Opciones elegidas por el mesero ---- */
.pos-item-options {
    color: #8e6a00;
    font-weight: 700;
}

/* ---- Estado: enviado a cocina ---- */
.pos-cart-item-sent {
    background: rgba(67,160,71,.05);
    border-radius: 10px;
    padding: 10px;
    margin-left: -10px;
    margin-right: -10px;
}

.badge-sent-kitchen {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .02em;
    color: #2e7d32;
    background: rgba(67,160,71,.14);
    border-radius: 20px;
    padding: 2px 8px;
}
</style>
@endpush

@push('script')
<script>
    function confirmRemoveWithAuth(itemId) {
        Swal.fire({
            title: 'Producto ya enviado a cocina',
            html: `
                <p class="text-muted small mb-2" style="text-align:left;">
                    Este producto ya fue enviado a preparacion. Quitarlo requiere
                    el PIN de autorizacion.
                </p>
                <select id="remove-reason" class="swal2-select" style="display:block;width:100%;margin:0 0 10px;">
                    <option value="">Selecciona un motivo</option>
                    <option value="cliente_cancelo">Cliente cancelo</option>
                    <option value="error_pedido">Error al tomar el pedido</option>
                    <option value="producto_agotado">Producto agotado en cocina</option>
                    <option value="otro">Otro motivo</option>
                </select>
                <input type="password" inputmode="numeric" maxlength="6" id="remove-pin" class="swal2-input" placeholder="PIN de autorizacion" style="margin:0;letter-spacing:4px;text-align:center;">
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Autorizar y quitar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#c62828',
            focusConfirm: false,
            preConfirm: () => {
                const reason = document.getElementById('remove-reason').value;
                const pin = document.getElementById('remove-pin').value;

                if (!reason) {
                    Swal.showValidationMessage('Selecciona un motivo');
                    return false;
                }

                if (!pin) {
                    Swal.showValidationMessage('Ingresa el PIN de autorizacion');
                    return false;
                }

                return { reason, pin };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.removeProductSecure(itemId, result.value.pin, result.value.reason);
            }
        });
    }
</script>
@endpush