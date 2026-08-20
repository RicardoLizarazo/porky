<div class="row justify-content-center">
    <div class="col-lg-6">

        @if($openSessions->isNotEmpty())
            <div class="alert alert-info">
                <strong><i class="fas fa-cash-register mr-1"></i> Ya tienes abiertas:</strong>
                {{ $openSessions->pluck('cashRegister.name')->filter()->implode(', ') }}
            </div>
        @endif

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
                        @if($cashRegisters->isEmpty())
                            <small class="form-text text-muted">
                                No tienes más cajas disponibles para abrir en este momento.
                            </small>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>
                            Fondo Inicial
                        </label>
                    
                        <input
                            type="text"
                            inputmode="numeric"
                            class="form-control"
                            x-data="{
                                raw: @entangle('opening_amount'),
                                display: ''
                            }"
                            x-init="display = raw ? new Intl.NumberFormat('es-CO').format(raw) : ''"
                            x-model="display"
                            @input="
                                let clean = $event.target.value.replace(/\D/g, '');
                                raw = clean ? parseInt(clean, 10) : 0;
                                display = clean ? new Intl.NumberFormat('es-CO').format(clean) : '';
                            "
                        >
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
