<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Nueva Ubicación
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form wire:submit.prevent="store">
                <div class="modal-body">

                    {{-- NOMBRE --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Ej: Sede Principal, Piso 2, Cocina..."
                               wire:model.defer="name">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Descripción</label>
                        <textarea
                            class="form-control @error('description') is-invalid @enderror"
                            rows="3"
                            placeholder="Información adicional de la ubicación"
                            wire:model.defer="description"></textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ESTADO --}}
                    <div class="form-group">
                        <label class="font-weight-bold d-block">Estado</label>

                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="is_active_create"
                                   wire:model.defer="is_active">
                            <label class="custom-control-label" for="is_active_create">
                                Activa
                            </label>
                        </div>

                        <small class="form-text text-muted">
                            Si está inactiva, no podrá ser utilizada en el sistema.
                        </small>
                    </div>

                    <hr>

<h6 class="font-weight-bold mb-3">
    <i class="fas fa-clock mr-1"></i>
    Horarios por día
</h6>

@php
$days = [
    'monday' => 'Lunes',
    'tuesday' => 'Martes',
    'wednesday' => 'Miércoles',
    'thursday' => 'Jueves',
    'friday' => 'Viernes',
    'saturday' => 'Sábado',
    'sunday' => 'Domingo',
];
@endphp

@foreach($days as $key => $label)
    <div class="border rounded p-2 mb-2">

        <div class="d-flex align-items-center justify-content-between">

            {{-- Día --}}
            <strong>{{ $label }}</strong>

            {{-- Activo --}}
            <div class="custom-control custom-switch">
                <input type="checkbox"
                       class="custom-control-input"
                       id="day_{{ $key }}"
                       wire:model="schedule.{{ $key }}.active">
                <label class="custom-control-label" for="day_{{ $key }}"></label>
            </div>

        </div>

        {{-- Horas --}}
        @if(data_get($schedule, $key.'.active'))
            <div class="row mt-2">

                <div class="col-6">
                    <input type="time"
                           class="form-control"
                           wire:model="schedule.{{ $key }}.start">
                </div>

                <div class="col-6">
                    <input type="time"
                           class="form-control"
                           wire:model="schedule.{{ $key }}.end">
                </div>

            </div>
        @endif

    </div>
@endforeach

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-cef btn-cef-cancel" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-cef btn-cef-create">
                        <span wire:loading.remove wire:target="store">
                            <i class="fas fa-save mr-1"></i>
                            Guardar
                        </span>
                        <span wire:loading wire:target="store">
                            <span class="spinner-border spinner-border-sm"></span>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>