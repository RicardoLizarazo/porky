<div class="container-fluid">
    @include('restaurante.cash-registers.create')
    @include('restaurante.cash-registers.edit')

    <div class="card">
        <div class="card-header">

            <h3>
                Cajas

                @can('cash-registers.create')
                    <button
                        class="btn btn-cef btn-cef-create"
                        wire:click="$dispatch('create')">

                        <i class="fas fa-plus"></i>

                    </button>
                @endcan

            </h3>

        </div>

        <div class="card-body">
            <livewire:cash-registers-table theme="bootstrap-4"/>
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