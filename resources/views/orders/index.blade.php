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
            @include('orders.edit')
            @include('orders.view')
            <div class="card">
                <div class="card-header">
                    <h3>Pedidos</h3>
                </div>

                <div class="card-body">
                    <livewire:orders-table theme="bootstrap-4" />
                </div>
            </div>        
        </div>
    </div>
</div>
@push('css')
<style>
    .dropdown-item.disabled {
        pointer-events: none;
        opacity: 0.6;
    }

    .btn-outline-primary {
        transition: all .2s ease;
    }

    .btn-outline-primary:hover {
        transform: scale(1.02);
    }
</style>
@endpush

@push('script')
<script>
        document.addEventListener('livewire:init', () => {
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

            Livewire.on('open-view-modal', () => {
                window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
                $("#modal-view").modal();
            });

            Livewire.on('deleteOrder', id => {
                Swal.fire({
                    title: '¿Eliminar pedido?',
                    text: "No podrás revertir esto",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('confirmDeleteOrder', id);
                    }
                });
            });

            Livewire.on('success', event => {
                Swal.fire({
                    icon: 'success',
                    title: event.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            });

            Livewire.on('order-error', event => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: event.message
                });
            });

            Livewire.on('check-order-focus', () => {
                let orderId = localStorage.getItem('order_focus_id');
                let filter  = localStorage.getItem('order_filter');

                if (filter === 'nuevo') {
                    Livewire.dispatch('apply-new-filter');
                }

                if (orderId) {
                    setTimeout(() => {
                        Livewire.dispatch('view-order', { id: orderId });
                        localStorage.removeItem('order_focus_id');
                    }, 500);
                }

                localStorage.removeItem('order_filter');
            });
        });     
    </script>
@endpush