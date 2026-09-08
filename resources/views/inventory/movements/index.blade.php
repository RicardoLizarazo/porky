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

    <div class="row">
        <div class="col-12">
            @include('inventory.movements.create')
            <div class="card">
                <div class="card-header">
                    <h3>Salidas y Ajustes de Inventario
                    @can('inventory_movements.create')
                        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
                            $dispatch('create')"
                            class="btn btn-cef btn-cef-create"
                            title="Registrar salida"
                            data-toggle="tooltip"
                        >
                            <i class="fas fa-plus"></i>
                        </button>
                    @endcan
                    </h3>
                </div>

                <div class="card-body">
                    <livewire:inventory.inventory-movements-table theme="bootstrap-4" />
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
            Swal.fire({ icon: 'success', title: 'Movimiento registrado correctamente', showConfirmButton: false, timer: 1500 });
        });

        Livewire.on('error', (message) => {
            Swal.fire({ icon: 'error', title: 'No se pudo completar la acción', text: Array.isArray(message) ? message[0] : message });
        });
    });
</script>
@endpush
