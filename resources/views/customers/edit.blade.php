<div class="modal fade" id="modal-edit" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    Editar Cliente
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="update">

                <div class="modal-body">

                    {{-- DATOS --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   wire:model.defer="name">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   wire:model.defer="email">
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Teléfono</label>
                            <input type="text"
                                   class="form-control @error('telephone') is-invalid @enderror"
                                   wire:model.defer="telephone">
                            @error('telephone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label>Estado</label>
                            <select class="form-control" wire:model.defer="status">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    {{-- DIRECCIONES --}}
                    <hr>
                    <h5>Direcciones</h5>

                    @foreach($addresses as $index => $addr)
                    <div class="card mb-3 shadow-sm">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Dirección #{{ $index + 1 }}</strong>

                                @if(count($addresses) > 1)
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="removeAddress({{ $index }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="text"
                                       class="form-control address-input"
                                       data-index="{{ $index }}"
                                       value="{{ $addr['address'] }}"
                                       placeholder="Buscar dirección...">
                            </div>

                            <div class="form-group">
                                <input type="text"
                                       class="form-control"
                                       wire:model.defer="addresses.{{ $index }}.reference"
                                       placeholder="Referencia (ej: casa esquinera)">
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       wire:model="defaultIndex"
                                       value="{{ $index }}">
                                <label class="form-check-label">
                                    Dirección principal
                                </label>
                            </div>

                        </div>
                    </div>
                    @endforeach

                    <button type="button"
                            class="btn btn-outline-primary btn-sm"
                            wire:click="addAddress">
                        + Agregar dirección
                    </button>

                    <hr>
                    
                    <h6 class="mb-3">
                    Cambiar contraseña (Opcional)
                    </h6>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Nueva contraseña</label>

                            <input type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                wire:model.defer="password">

                            @error('password')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label>Confirmar contraseña</label>

                            <input type="password"
                                class="form-control"
                                wire:model.defer="password_confirmation">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Actualizar</button>
                </div>

            </form>

        </div>
    </div>
</div>