<div class="row justify-content-center">

    <div class="col-lg-6">

        <div class="card card-primary">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-lock-open mr-2"></i>

                    Apertura de Caja

                </h3>

            </div>

            <form wire:submit.prevent="open">

                <div class="card-body">

                    <div class="form-group">

                        <label>

                            Caja
                        </label>

                        <select
                            class="form-control"
                            wire:model="cash_register_id">

                            <option value="">

                                Seleccione

                            </option>

                            @foreach($cashRegisters as $cash)

                                <option
                                    value="{{ $cash->id }}">

                                    {{ $cash->name }}

                                    -
                                    {{ $cash->floor?->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="form-group">

                        <label>

                            Fondo Inicial
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            class="form-control"
                            wire:model="opening_amount">

                    </div>

                    <div class="form-group">

                        <label>

                            Observación
                        </label>

                        <textarea
                            rows="3"
                            class="form-control"
                            wire:model="opening_notes">
                        </textarea>

                    </div>

                </div>

                <div class="card-footer text-right">

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="fas fa-lock-open"></i>

                        Abrir Caja

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>