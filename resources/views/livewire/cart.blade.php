<div>
    @if($open)

    <livewire:components.customer-inline-create />
    <livewire:components.customer-inline-edit />

    <div class="cart-overlay" wire:click="close"></div>

    <div class="cart-sidebar">

        <div class="cart-header">
            <h5>🛒 Tu pedido</h5>
            <button wire:click="close">✕</button>
        </div>

        <div class="cart-body">

            @forelse($this->cart as $item)

                <div class="cart-item">

                    <img src="{{ $item['image'] 
                        ? asset('storage/'.$item['image']) 
                        : asset('images/no-image.png') }}">

                    <div class="flex-grow-1 ml-2">
                        <div class="font-weight-bold">{{ $item['name'] }}</div>

                        <small>
                            Producto: ${{ number_format($item['price']) }}
                        </small>

                        @if(($item['packaging_cost'] ?? 0) > 0)
                            <small class="d-block text-muted">
                                Empaque: ${{ number_format($item['packaging_cost']) }}
                            </small>
                        @endif
                    </div>

                    <div class="qty-control-modern">
                        <button wire:click="remove({{ $item['id'] }})">−</button>
                        <span>{{ $item['quantity'] }}</span>
                        <button wire:click="add({{ $item['id'] }})">+</button>
                    </div>

                </div>

            @empty
                <div class="text-center text-muted mt-4">
                    Tu carrito está vacío
                </div>
            @endforelse

            @auth
                {{-- Solo si NO es customer --}}
                @if(!auth('customer')->check())
                    <div class="mb-2">
                        <label>Cliente</label>

                        <livewire:components.customer-autocomplete
                            event="customerSelected"
                            :selected-id="$customer_id"
                            :key="'customer-'.$customer_id"
                        />

                        @if($this->selectedCustomer)
                            <div class="customer-card mt-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="font-weight-bold text-dark">
                                            👤 {{ $this->selectedCustomer->name }}
                                        </div>

                                        <div class="text-muted small mt-1">
                                            📞 {{ $this->selectedCustomer->telephone ?? 'Sin teléfono' }}
                                        </div>

                                        @if($this->selectedCustomer->defaultAddress)
                                            <div class="text-muted small">
                                                📍 {{ $this->selectedCustomer->defaultAddress->address }}
                                            </div>
                                        @endif
                                    </div>
                                    <button 
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="$set('customer_id', null)"
                                        title="Quitar cliente"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @endauth

            {{-- Indicaciones y comentarios --}}
            <div class="mt-3">

                <div class="form-group">
                    <label>📝 Indicaciones</label>
                    <textarea 
                        wire:model.defer="indication"
                        class="form-control"
                        rows="2"
                        placeholder="Nombre del conjunto, numero de torre, apartamento, etc."
                    ></textarea>
                </div>

                <div class="form-group">
                    <label>💬 Comentarios</label>
                    <textarea 
                        wire:model.defer="comment"
                        class="form-control"
                        rows="2"
                        placeholder="Ej: sin cebolla, término medio, etc."
                    ></textarea>
                </div>

            </div>

        </div>

        <div class="cart-footer">

            {{-- Productos --}}
            <div class="d-flex justify-content-between small">
                <span>Productos</span>
                <span>${{ number_format($this->subtotal) }}</span>
            </div>

            {{-- Empaque --}}
            @if($this->packagingTotal > 0)
                <div class="d-flex justify-content-between small text-muted">
                    <span>Empaque</span>
                    <span>${{ number_format($this->packagingTotal) }}</span>
                </div>
            @endif

            {{-- Domicilio --}}
            <div class="d-flex justify-content-between align-items-center small mt-2">
                <span>🛵 Domicilio</span>

                @if(auth('customer')->check())
                    {{-- 👤 CLIENTE: solo visual --}}
                    <span class="font-weight-bold">
                        ${{ number_format($this->delivery_cost) }}
                    </span>
                @else
                    {{-- 👨‍💼 ADMIN: editable --}}
                    <input 
                        type="number"
                        min="0"
                        step="500"
                        wire:model.lazy="delivery_cost"
                        class="form-control form-control-sm text-right"
                        style="width: 100px;"
                    >
                @endif
            </div>

            <hr class="my-2">

            {{-- Total --}}
            <div class="d-flex justify-content-between">
                <span>Total</span>
                <strong class="text-danger">
                    ${{ number_format($this->total) }}
                </strong>
            </div>

            {{-- Mensaje --}}
            @if($this->packagingTotal > 0 || $this->delivery_cost > 0)
                <div class="alert alert-light mt-2 p-2 small text-muted">
                    ℹ️ El total incluye productos, empaque y domicilio.
                </div>
            @endif

            <button wire:click="confirm"
                    class="btn btn-danger btn-block mt-3">
                Confirmar pedido
            </button>

        </div>

    </div>

    @endif
</div>

@push('css')
    <style>
        .customer-card {
            background: #fff;
            border: 1px solid #eee;
            border-left: 4px solid #dc3545; /* rojo marca */
            border-radius: 10px;
            padding: 12px 14px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }

        .customer-card:hover {
            box-shadow: 0 6px 14px rgba(0,0,0,0.08);
        }
    </style>    
@endpush