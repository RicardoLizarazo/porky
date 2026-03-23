<div class="modal fade" id="modal-create" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-box mr-2"></i>
                    Nuevo Producto
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form wire:submit.prevent="store">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold mb-2">
                                    <i class="fas fa-image mr-1"></i> Imagen del producto
                                </label>
                                
                                {{-- Contenedor de imagen --}}
                                <div class="card">
                                    <div class="card-body text-center">
                                        {{-- Preview de imagen --}}
                                        @if ($image)
                                            <div class="d-flex justify-content-center">
                                                <img src="{{ $image->temporaryUrl() }}"
                                                    class="img-fluid rounded"
                                                    style="max-height: 200px; width: auto; object-fit: contain;">
                                            </div>
                                            <div class="text-muted small mt-2">Imagen seleccionada</div>
                                        @else
                                            <div class="py-4">
                                                <i class="fas fa-camera fa-3x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No hay imagen seleccionada</p>
                                                <small class="text-muted">Formatos: JPG, PNG, GIF (Max 2MB)</small>
                                            </div>
                                        @endif
                                        
                                        {{-- Botón de carga --}}
                                        <div class="mt-3">
                                            <label class="btn btn-outline-primary btn-sm mb-0">
                                                <i class="fas fa-upload mr-1"></i>
                                                {{ $image ? 'Cambiar imagen' : 'Seleccionar imagen' }}
                                                <input type="file"
                                                    class="d-none"
                                                    wire:model="image"
                                                    accept="image/*">
                                            </label>
                                        </div>
                                        
                                        {{-- Loading indicator --}}
                                        <div wire:loading wire:target="image" class="mt-2">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="sr-only">Cargando...</span>
                                            </div>
                                            <span class="text-muted small ml-2">Subiendo imagen...</span>
                                        </div>
                                        
                                        {{-- Error message --}}
                                        @error('image')
                                            <div class="text-danger small mt-2">
                                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- NOMBRE --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       wire:model.defer="name">
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- CÓDIGO --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Código</label>
                                <input type="text"
                                       class="form-control"
                                       wire:model.defer="code">
                            </div>
                        </div>

                        {{-- CATEGORÍA --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Categoría</label>
                                <select class="form-control @error('category_id') is-invalid @enderror"
                                        wire:model.defer="category_id">
                                    <option value="">Seleccione</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- PRECIO --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Precio</label>
                                <input type="number" step="0.01"
                                       class="form-control @error('price') is-invalid @enderror"
                                       wire:model.defer="price">
                                @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- EMPAQUE --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Costo de empaque</label>
                                <input type="number" step="0.01"
                                       class="form-control"
                                       wire:model.defer="packaging_cost">
                            </div>
                        </div>

                        {{-- DESCRIPCIÓN --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Descripción</label>
                                <textarea class="form-control"
                                          rows="3"
                                          wire:model.defer="description"></textarea>
                            </div>
                        </div>

                    </div>

                    <hr>

                    {{-- SWITCHES --}}
                    <div class="row">

                        <div class="col-md-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="visible_create"
                                       wire:model.defer="is_visible">
                                <label class="custom-control-label" for="visible_create">
                                    Visible al público
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="active_create"
                                       wire:model.defer="is_active">
                                <label class="custom-control-label" for="active_create">
                                    Activo
                                </label>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-cef btn-cef-cancel" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-cef btn-cef-create">
                        Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>