@if($order->packaging_total > 0)
    <div class="d-flex justify-content-between small mb-2">
        <span><i class="fas fa-box mr-1"></i> Empaque</span>
        <span>
            ${{ number_format($order->packaging_total, 0, ',', '.') }}
        </span>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <strong class="pos-footer-total-label">Total</strong>
    <strong class="pos-footer-total-amount">
        ${{ number_format($this->total, 0, ',', '.') }}
    </strong>
</div>

<button wire:click="sendToKitchen" class="btn-send-kitchen">
    <i class="fas fa-fire mr-2"></i>
    Enviar a Cocina
</button>

@push('css')
<style>
.pos-footer-total-label {
    font-size: 1rem;
    color: var(--brand-dark, #140a0a);
}
.pos-footer-total-amount {
    font-size: 1.4rem;
    color: var(--brand-primary-dark, #8e0000);
}
.btn-send-kitchen {
    width: 100%;
    min-height: 52px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(90deg, var(--brand-secondary), var(--brand-primary));
    color: #fff;
    font-weight: 800;
    font-size: 1rem;
    box-shadow: 0 6px 16px rgba(198,40,40,.3);
}
.btn-send-kitchen:active {
    transform: scale(.98);
}
</style>
@endpush