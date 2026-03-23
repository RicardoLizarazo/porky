<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-user-plus mr-2"></i>
                    Nuevo Usuario
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form wire:submit.prevent="store">
                <div class="modal-body">

                    {{-- DATOS PERSONALES --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombres</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Escribe los nombres"
                                       wire:model.defer="name">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Email</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="correo@dominio.com"
                                       wire:model.defer="email">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- CONTRASEÑAS --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Contraseña</label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres"
                                       wire:model.defer="password">
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Confirmar contraseña</label>
                                <input type="password"
                                       class="form-control"
                                       placeholder="Repite la contraseña"
                                       wire:model.defer="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- ROLES --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Roles asignados</label>
                                <select wire:model.defer="roles"
                                        multiple
                                        class="form-control @error('roles') is-invalid @enderror">
                                    @foreach ($allRoles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ $role->description ?? $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Use <strong>Ctrl</strong> (Windows) o <strong>Cmd</strong> (Mac) para seleccionar múltiples roles.
                                </small>
                                @error('roles')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove wire:target="store">
                            <i class="fas fa-save mr-1"></i>
                            Guardar
                        </span>
                        <span wire:loading wire:target="store">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
