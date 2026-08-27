<div class="modal fade" id="modal-options" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-sliders-h mr-2"></i>
                    Opciones — {{ $optionProductName }}
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <small class="text-muted d-block mb-3">
                    Al agregar este producto al pedido, el mesero deberá elegir
                    <strong>una</strong> de estas opciones. Si la lista queda vacía,
                    el producto se agrega directo, sin preguntar.
                </small>

                @forelse($productOptions as $i => $row)
                    <div class="input-group mb-2" wire:key="opt-{{ $i }}-{{ $row['id'] ?? 'new' }}">

                        <input type="text"
                               class="form-control"
                               placeholder="Ej. Sin azúcar"
                               wire:model.blur="productOptions.{{ $i }}.name">

                        <div class="input-group-append">
                            <button type="button"
                                    class="btn btn-outline-danger"
                                    wire:click="removeOptionRow({{ $i }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                    </div>
                @empty
                    <p class="text-muted mb-2">
                        Este producto no tiene opciones configuradas.
                    </p>
                @endforelse

                <button type="button"
                        class="btn btn-outline-primary btn-sm mt-2"
                        wire:click="addOptionRow">
                    <i class="fas fa-plus mr-1"></i> Agregar opción
                </button>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-cef btn-cef-cancel" data-dismiss="modal">
                    Cancelar
                </button>

                <button type="button" class="btn btn-cef btn-cef-create" wire:click="saveOptions">
                    Guardar
                </button>
            </div>

        </div>
    </div>
</div>