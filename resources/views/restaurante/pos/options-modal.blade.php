@if($showOptionsModal)
<div class="modal fade show pos-options-modal" style="display:block;" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">{{ $optionsProductName }}</h5>
            </div>

            <div class="modal-body">

                <small class="text-muted d-block mb-3">Selecciona una opcion</small>

                @foreach($optionsList as $opt)
                    <button
                        type="button"
                        class="pos-option-chip {{ $optionsSelected === $opt ? 'active' : '' }}"
                        wire:click="toggleOptionValue(@js($opt))"
                    >
                        {{ $opt }}
                    </button>
                @endforeach

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary pos-option-btn"
                    wire:click="closeOptionsModal"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="btn pos-option-btn pos-option-confirm"
                    wire:click="confirmOptions"
                    @if(! $optionsSelected) disabled @endif
                >
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
    z-index: 1060;
}

.pos-options-modal .modal-content {
    border-radius: 16px;
}

/* Una opcion por fila: blanco grande para el dedo */
.pos-option-chip {
    display: block;
    width: 100%;
    text-align: left;
    min-height: 58px;
    margin-bottom: 8px;
    padding: 14px 16px;
    border: 2px solid var(--brand-border, #f1d2bd);
    border-radius: 12px;
    background: #fff;
    color: var(--brand-dark, #140a0a);
    font-weight: 700;
    font-size: 1rem;
}

.pos-option-chip.active {
    background: var(--brand-primary, #c62828);
    border-color: var(--brand-primary, #c62828);
    color: #fff;
}

.pos-option-btn {
    flex: 1 1 0;
    min-height: 52px;
    margin: 0 4px;
    border-radius: 12px;
    font-weight: 800;
}

.pos-option-confirm {
    border: none;
    background: var(--brand-primary, #c62828);
    color: #fff;
}

.pos-option-confirm:hover {
    color: #fff;
}

.pos-option-confirm[disabled] {
    opacity: .4;
}

/* Celular: hoja inferior, al alcance del pulgar */
@media (max-width: 767.98px) {

    .pos-options-modal .modal-dialog {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        margin: 0;
        max-width: 100%;
        min-height: auto;
        align-items: flex-end;
    }

    .pos-options-modal .modal-content {
        border-radius: 20px 20px 0 0;
        max-height: 90vh;
    }

    .pos-options-modal .modal-body {
        overflow-y: auto;
    }

    .pos-options-modal .modal-footer {
        flex-wrap: nowrap;
        padding-bottom: calc(12px + env(safe-area-inset-bottom, 0px));
    }
}
</style>
@endpush