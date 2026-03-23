<div
    class="container-fluid"
    x-data="{ showLoading: false }"
    @toggle-loading.window="showLoading = $event.detail"
>
    {{-- Spinner --}}
    <template x-if="showLoading">
        <div
            class="position-fixed w-100 h-100 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; background: rgba(255,255,255,0.6); z-index: 9999;"
        >
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-dark font-weight-bold">Cargando información...</p>
            </div>
        </div>
    </template>

    <div class="row">
        <div class="col-12">
            @include('security.permissions.create')
            @include('security.permissions.edit')
            <div class="card">
                <div class="card-header">
                    <h3>Permisos
                    @can('permissions.create')
                        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('create')" class="btn btn-cef btn-cef-create" title="Crear" data-toggle="tooltip"><i class="fas fa-plus"></i></button>
                    @endcan
                    </h3>
                </div>

                <div class="card-body">
                    <livewire:permissions-table/>
                </div>
            </div>        
        </div>
    </div>
</div>
@push('script')
<script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-create-modal', () => {
                window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
                $("#modal-create").modal();
            });

            Livewire.on('store', () => {
            	$('#modal-create').modal('hide');
                Swal.fire({
                  icon: 'success',
                  title: 'Registro creado correctamente',
                  showConfirmButton: false,
                  timer: 1500
                })
            });

            Livewire.on('open-edit-modal', () => {
                window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
                $("#modal-edit").modal();
            });

            Livewire.on('update', () => {
            	$('#modal-edit').modal('hide');
                Swal.fire({
                  icon: 'success',
                  title: 'Registro actualizado correctamente',
                  showConfirmButton: false,
                  timer: 1500
                })
            });

            Livewire.on('delete', id =>{
                Swal.fire({
                  title: 'Esta seguro de eliminar el registro?',
                  text: "¡No podrás revertir esto!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Si',
                  cancelButtonText: 'No'
                }).then((result) => {
                  if (result.isConfirmed) {
                  	@this.call('delete', id)
                    Swal.fire({
                        title: 'Eliminado',
                        text: 'El registro ha sido eliminado',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    })
                  }
                })
            });
        });     
    </script>
@endpush