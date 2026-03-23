<div class="modal fade" id="modal-edit" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form wire:submit.prevent="update">

                {{-- HEADER --}}
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit mr-2"></i>
                        Editar Usuario
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nombres</label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           wire:model.defer="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Email</label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           wire:model.defer="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nueva contraseña</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Dejar en blanco para no cambiar"
                                           wire:model.defer="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Confirmar contraseña</label>
                                    <input type="password"
                                           class="form-control"
                                           wire:model.defer="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="font-weight-bold">Roles</label>
                            <select wire:model.defer="roles"
                                    multiple
                                    class="form-control @error('roles') is-invalid @enderror">
                                @foreach ($allRoles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->description ?? $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove wire:target="update">
                            <i class="fas fa-save mr-1"></i>
                            Actualizar
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm"></span>
                            Actualizando…
                        </span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
