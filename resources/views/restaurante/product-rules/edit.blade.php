<div class="modal fade" id="modal-edit" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-random mr-2"></i>
                    Editar Regla
                </h4>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            {{-- FORM --}}
            <form wire:submit.prevent="update">

                <div class="modal-body">

                    {{-- NOMBRE --}}
                    <div class="form-group">

                        <label class="font-weight-bold">

                            Nombre

                        </label>

                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Ej: Cambio Papa por Rellena"
                            wire:model.defer="name">

                        @error('name')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="form-group">

                        <label class="font-weight-bold">

                            Descripción

                        </label>

                        <textarea
                            rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            wire:model.defer="description">
                        </textarea>

                        @error('description')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    {{-- COSTO EXTRA --}}
                    <div class="form-group">

                        <label class="font-weight-bold">

                            Costo adicional

                        </label>

                        <input
                            type="number"
                            min="0"
                            step="1"
                            class="form-control @error('extra_price') is-invalid @enderror"
                            wire:model.defer="extra_price">

                        @error('extra_price')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    {{-- ACTIVA --}}
                    <div class="custom-control custom-switch">

                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="rule_active"
                            wire:model.defer="is_active">

                        <label
                            class="custom-control-label"
                            for="rule_active">

                            Regla Activa

                        </label>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-cef btn-cef-cancel" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-cef btn-cef-edit">
                        <span wire:loading.remove wire:target="update">
                            <i class="fas fa-save mr-1"></i>
                            Actualizar
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm"></span>
                            Guardando...
                        </span>
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>