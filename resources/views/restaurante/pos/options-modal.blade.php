@if($showOptionsModal)
<div class="modal fade show pos-options-modal" style="display:block;" wire:ignore.self>

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-sliders-h mr-2"></i>
                    {{ $optionsProductName }}
                </h5>
            </div>

            <div class="modal-body">

                <small class="text-muted d-block mb-3">
                    Selecciona la preparación
                </small>

                <div class="d-flex flex-wrap">
                    @foreach($optionsList as $opt)
                        <button
                            type="button"
                            class="pos-option-chip {{ $optionsSelected === $opt ? 'active' : '' }}"
                            wire:click="toggleOptionValue(@js($opt))"
                        >
                            @if($optionsSelected === $opt)
                                <i class="fas fa-check mr-2"></i>
                            @endif
                            {{ $opt }}
                        </button>
                    @endforeach
                </div>

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-lg btn-secondary"
                    wire:click="closeOptionsModal"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="btn btn-lg pos-option-confirm"
                    wire:click="confirmOptions"
                    @if(! $optionsSelected) disabled @endif
                >
                    <i class="fas fa-plus mr-1"></i>
                    Agregar
                </button>
            </div>

        </div>
    </div>

</div>
@endif

@push('css')
<style>
.pos-options-modal {
    background: rgba(20,10,10,.55);
}

.pos-options-modal .modal-content {
    border-radius: 16px;
    overflow: hidden;
}

.pos-option-chip {
    border: 2px solid var(--brand-border, #f1d2bd);
    background: #fff;
    color: var(--brand-dark, #140a0a);
    border-radius: 12px;
    padding: 14px 18px;
    margin: 4px;
    font-weight: 700;
    font-size: .95rem;
    min-height: 54px;
    flex-grow: 1;
}

.pos-option-chip.active {
    background: linear-gradient(135deg, var(--brand-secondary), var(--brand-primary));
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 12px rgba(198,40,40,.3);
}

.pos-option-chip:active {
    transform: scale(.97);
}

.pos-option-confirm {
    border: none;
    background: linear-gradient(90deg, var(--brand-secondary), var(--brand-primary));
    color: #fff;
    font-weight: 800;
}

.pos-option-confirm:hover {
    color: #fff;
}

.pos-option-confirm[disabled] {
    opacity: .4;
    box-shadow: none;
}
</style>
@endpush