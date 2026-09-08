<div
    class="container-fluid"
    x-data="{ showLoading: false }"
    @toggle-loading.window="showLoading = $event.detail"
>
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

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-warehouse"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valor total de inventario</span>
                    <span class="info-box-number">${{ number_format($totalValue, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @include('inventory.inventory-items.create')
            @include('inventory.inventory-items.edit')
            <div class="card">
                <div class="card-header">
                    <h3>Productos de Inventario
                    @can('inventory_items.create')
                        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
                            $dispatch('create')"
                            class="btn btn-cef btn-cef-create"
                            title="Crear"
                            data-toggle="tooltip"
                        >
                            <i class="fas fa-plus"></i>
                        </button>
                    @endcan
                    </h3>
                </div>

                <div class="card-body">
                    <livewire:inventory.inventory-items-table theme="bootstrap-4" />
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
            $('#modal-create').modal('show');
        });

        Livewire.on('store', () => {
            $('#modal-create').modal('hide');
            Swal.fire({ icon: 'success', title: 'Registro creado correctamente', showConfirmButton: false, timer: 1500 });
        });

        Livewire.on('open-edit-modal', () => {
            window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
            $('#modal-edit').modal('show');
        });

        Livewire.on('update', () => {
            $('#modal-edit').modal('hide');
            Swal.fire({ icon: 'success', title: 'Registro actualizado correctamente', showConfirmButton: false, timer: 1500 });
        });

        Livewire.on('delete', id => {
            Swal.fire({
                title: '¿Está seguro de eliminar el registro?',
                text: 'No podrás revertir esto',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', id);
                }
            });
        });

        Livewire.on('error', (message) => {
            Swal.fire({ icon: 'error', title: 'No se pudo completar la acción', text: Array.isArray(message) ? message[0] : message });
        });
    });
</script>
@endpush
