<div>
    @if($this->count > 0 && !$open)
        <div class="cart-fab" wire:click="$dispatch('open-cart')">
            <div class="cart-fab-content">
                🛒 {{ $this->count }}
                <span class="ml-2">
                    ${{ number_format($this->total,0,',','.') }}
                </span>
            </div>

        </div>
    @endif
</div>