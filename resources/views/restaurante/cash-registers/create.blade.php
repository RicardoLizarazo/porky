<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">

                <h4 class="modal-title">

                    <i class="fas fa-cash-register mr-2"></i>

                    Nueva Caja

                </h4>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            {{-- FORM --}}
            <form wire:submit.prevent="store">

                <div class="modal-body">

                    {{-- SEDE --}}
                    <div class="form-group">

                        <label class="font-weight-bold">

                            Sede

                        </label>

                        <select
                            class="form-control @error('location_id') is-invalid @enderror"
                            wire:model.defer="location_id">

                            <option value="">

                                Seleccione una sede

                            </option>

                            @foreach($locations as $location)

                                <option value="{{ $location->id }}">

                                    {{ $location->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('location_id')

                            <span class="invalid-feedback">

                                {{ $message }}

                            </span>

                        @enderror

                    </div>

                    {{-- PISO --}}
                    <div class="form-group">

                        <label class="font-weight-bold">

                            Piso

                        </label>

                        <select
                            class="form-control @error('floor_id') is-invalid @enderror"
                            wire:model.defer="floor_id">

                            <option value="">

                                Seleccione un piso

                            </option>

                            @foreach($floors as $floor)

                                <option value="{{ $floor->id }}">

                                    {{ $floor->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('floor_id')

                            <span class="invalid-feedback">

                                {{ $message }}

                            </span>

                        @enderror

                    </div>

                    {{-- RESPONSABLE --}}
                    <div class="form-group">

                        <label class="font-weight-bold">
                            Responsable
                        </label>

                        <select
                            class="form-control @error('responsible_user_id') is-invalid @enderror"
                            wire:model.defer="responsible_user_id">

                            <option value="">
                                Sin responsable asignado
                            </option>

                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('responsible_user_id')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                        <small class="text-muted">
                            Este usuario será el responsable habitual de la caja. Cualquier cajero con permisos podrá operarla mediante una sesión de caja.
                        </small>
                    </div>

                    {{-- NOMBRE --}}
                    <div class="form-group">

                        <label class="font-weight-bold">

                            Nombre

                        </label>

                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Ej: Caja Piso 1"
                            wire:model.defer="name">

                        @error('name')

                            <span class="invalid-feedback">

                                {{ $message }}

                            </span>

                        @enderror

                    </div>

                    {{-- ESTADO --}}
                    <div class="row">

                        <div class="col-md-12">

                            <div class="custom-control custom-switch">

                                <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="active_create"
                                    wire:model.defer="is_active">

                                <label
                                    class="custom-control-label"
                                    for="active_create">

                                    Caja Activa

                                </label>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-cef btn-cef-cancel"
                        data-dismiss="modal">

                        <i class="fas fa-times mr-1"></i>

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="btn btn-cef btn-cef-create">

                        <span
                            wire:loading.remove
                            wire:target="store">

                            <i class="fas fa-save mr-1"></i>

                            Guardar

                        </span>

                        <span
                            wire:loading
                            wire:target="store">

                            <span class="spinner-border spinner-border-sm"></span>

                            Guardando...

                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>