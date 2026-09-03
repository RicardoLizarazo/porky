<div class="vitrina-lock-wrapper">

    <div class="vitrina-lock-card">

        <div class="vitrina-lock-icon">
            <i class="fas fa-store"></i>
        </div>

        <h4 class="vitrina-lock-title">Vitrina</h4>
        <p class="vitrina-lock-subtitle">Ingresa el PIN para tomar un pedido</p>

        {{-- INDICADOR DE DIGITOS --}}
        <div class="vitrina-pin-dots {{ $error ? 'vitrina-pin-error' : '' }}">
            @for($i = 0; $i < 4; $i++)
                <span class="vitrina-pin-dot {{ strlen($pin) > $i ? 'filled' : '' }}"></span>
            @endfor
        </div>

        @if($error)
            <div class="vitrina-lock-error-text">
                <i class="fas fa-exclamation-circle"></i> PIN incorrecto
            </div>
        @endif

        {{-- TECLADO NUMERICO --}}
        <div class="vitrina-keypad">
            @foreach([1,2,3,4,5,6,7,8,9] as $num)
                <button type="button" wire:click="pressDigit({{ $num }})" class="vitrina-key">
                    {{ $num }}
                </button>
            @endforeach

            <button type="button" wire:click="clearPin" class="vitrina-key vitrina-key-action">
                <i class="fas fa-times"></i>
            </button>

            <button type="button" wire:click="pressDigit(0)" class="vitrina-key">
                0
            </button>

            <button type="button" wire:click="backspace" class="vitrina-key vitrina-key-action">
                <i class="fas fa-backspace"></i>
            </button>
        </div>

    </div>

</div>

@push('css')
<style>
.vitrina-lock-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(160deg, var(--brand-dark, #140a0a), var(--brand-primary-dark, #8e0000));
    padding: 20px;
}

.vitrina-lock-card {
    background: #fff;
    border-radius: 24px;
    padding: 32px 28px;
    width: 100%;
    max-width: 360px;
    text-align: center;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
}

.vitrina-lock-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--brand-secondary), var(--brand-primary));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin: 0 auto 14px;
}

.vitrina-lock-title {
    font-weight: 800;
    color: var(--brand-dark, #140a0a);
    margin-bottom: 2px;
}

.vitrina-lock-subtitle {
    color: #888;
    font-size: .85rem;
    margin-bottom: 22px;
}

.vitrina-pin-dots {
    display: flex;
    justify-content: center;
    gap: 14px;
    margin-bottom: 8px;
}

.vitrina-pin-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid var(--brand-border, #f1d2bd);
    background: #fff;
    transition: all .15s ease;
}

.vitrina-pin-dot.filled {
    background: var(--brand-primary);
    border-color: var(--brand-primary);
}

.vitrina-pin-error .vitrina-pin-dot {
    border-color: #c62828;
}

.vitrina-lock-error-text {
    color: #c62828;
    font-size: .82rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.vitrina-keypad {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 20px;
}

.vitrina-key {
    aspect-ratio: 1;
    border: none;
    border-radius: 16px;
    background: var(--brand-light-alt, #fbe9e7);
    color: var(--brand-dark, #140a0a);
    font-size: 1.4rem;
    font-weight: 800;
}

.vitrina-key:active {
    transform: scale(.94);
    background: var(--brand-border, #f1d2bd);
}

.vitrina-key-action {
    background: transparent;
    color: #999;
    font-size: 1.1rem;
}
</style>
@endpush

@push('script')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('swal', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                icon: data.icon || 'info',
                title: data.title || '',
            });
        });
    });
</script>
@endpush