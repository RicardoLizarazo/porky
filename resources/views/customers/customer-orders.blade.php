<div
    class="container-fluid"
    x-data="{ showLoading: false }"
    @toggle-loading.window="showLoading = $event.detail"
>

    {{-- Spinner --}}
    <template x-if="showLoading">
        <div class="position-fixed w-100 h-100 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; background: rgba(255,255,255,0.6); z-index: 9999;">
            <div class="text-center">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2 text-dark font-weight-bold">Cargando información...</p>
            </div>
        </div>
    </template>

    {{-- 🔥 MODAL CLIENTE --}}
    @include('customers.orders.view')

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <h3>Mis pedidos</h3>
                </div>

                <div class="card-body">
                    <livewire:customer-orders-table :customerId="auth('customer')->id()" />
                </div>
            </div>        

        </div>
    </div>
</div>
@push('script')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-customer-view-modal', () => {
                window.dispatchEvent(new CustomEvent('toggle-loading', { detail: false }));
                $("#modal-customer-view").modal();
            });
        });     
    </script>
@endpush