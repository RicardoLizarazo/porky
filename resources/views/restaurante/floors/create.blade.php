<div wire:ignore.self class="modal fade" id="modal-create">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary">
                <h5 class="modal-title">Crear Piso</h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit="store">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Ubicación</label>

                        <select
                            wire:model="location_id"
                            class="form-control"
                        >
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
                        <label>Nombre</label>

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
                        <label>Orden</label>

                        <input
                            type="number"
                            wire:model="sort_order"
                            class="form-control"
                        >

                        @error('sort_order')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="form-check-input"
                            id="activeCreate"
                        >

                        <label class="form-check-label" for="activeCreate">
                            Activo
                        </label>
                    </div>

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