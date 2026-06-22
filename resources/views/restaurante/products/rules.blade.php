<div
    class="modal fade"
    id="modal-rules"
    tabindex="-1"
    wire:ignore.self
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">

                <h4 class="modal-title">

                    <i class="fas fa-random mr-2"></i>

                    Reglas del Producto

                </h4>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal"
                >
                    <span>&times;</span>
                </button>

            </div>

            {{-- BODY --}}
            <div class="modal-body">

                <div class="alert alert-light">

                    <strong>Producto:</strong>

                    {{ $productName }}

                </div>

                <div class="row">

                    @foreach($availableRules as $rule)

                        <div class="col-md-6 mb-3">

                            <div class="custom-control custom-checkbox">

                                <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="rule{{ $rule->id }}"
                                    value="{{ $rule->id }}"
                                    wire:model="selectedRules"
                                >

                                <label
                                    class="custom-control-label"
                                    for="rule{{ $rule->id }}"
                                >

                                    <strong>
                                        {{ $rule->name }}
                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $rule->description }}

                                    </small>

                                    @if($rule->extra_price > 0)

                                        <span class="badge badge-success ml-1">

                                            +${{ number_format($rule->extra_price,0,',','.') }}

                                        </span>

                                    @endif

                                </label>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-cef btn-cef-cancel"
                    data-dismiss="modal"
                >
                    Cancelar
                </button>

                <button
                    wire:click="saveRules"
                    class="btn btn-cef btn-cef-create"
                >

                    <i class="fas fa-save mr-1"></i>

                    Guardar Reglas

                </button>

            </div>

        </div>

    </div>

</div>