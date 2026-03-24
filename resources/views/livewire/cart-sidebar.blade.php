<div>
    @if($open)

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
                        <small>${{ number_format($item['price']) }}</small>
                    </div>

                    <div class="qty-control-modern">
                        <button wire:click="decrement({{ $item['id'] }})">−</button>
                        <span>{{ $item['quantity'] }}</span>
                        <button wire:click="increment({{ $item['id'] }})">+</button>
                    </div>

                </div>

            @empty
                <div class="text-center text-muted mt-4">
                    Tu carrito está vacío
                </div>
            @endforelse

        </div>

        <div class="cart-footer">

            <div class="d-flex justify-content-between">
                <span>Total</span>
                <strong>${{ number_format($this->total) }}</strong>
            </div>

            <button class="btn btn-danger btn-block mt-3">
                Continuar pedido
            </button>

        </div>

    </div>

    @endif
</div>
