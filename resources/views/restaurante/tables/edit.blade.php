<div wire:ignore.self class="modal fade" id="modal-edit">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary">
                <h5 class="modal-title">Editar Mesa</h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="update">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Ubicación</label>

                        <select wire:model="location_id" class="form-control">
                            <option value="">Seleccione</option>

                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('location_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Piso</label>

                        <select wire:model="floor_id" class="form-control">
                            <option value="">Seleccione</option>

                            @foreach($floors as $floor)
                                <option value="{{ $floor->id }}">
                                    {{ $floor->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('floor_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Nombre Mesa</label>

                        <input
                            type="text"
                            wire:model="name"
                            class="form-control"
                        >

                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Capacidad</label>

                        <input
                            type="number"
                            wire:model="capacity"
                            class="form-control"
                        >

                        @error('capacity')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Estado</label>

                        <select wire:model="status" class="form-control">

                            <option value="available">
                                Disponible
                            </option>

                            <option value="occupied">
                                Ocupada
                            </option>

                            <option value="reserved">
                                Reservada
                            </option>

                            <option value="cleaning">
                                Limpieza
                            </option>

                            <option value="pending_payment">
                                Pago Pendiente
                            </option>

                        </select>
                    </div>

                    <div class="form-check">

                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="form-check-input"
                            id="activeCreate"
                        >

                        <label
                            class="form-check-label"
                            for="activeCreate"
                        >
                            Activa
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