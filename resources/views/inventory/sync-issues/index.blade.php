<div class="container-fluid">

    <div class="alert alert-info">
        Estos productos se vendieron sin tener un producto de inventario vinculado, por lo que la venta se
        completó normal pero <strong>no descontó existencias</strong>. Vincúlalos desde "Productos" y marca el
        pendiente como resuelto.
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Pendientes de Inventario</h3>
        </div>
        <div class="card-body">
            <livewire:inventory.inventory-sync-issues-table theme="bootstrap-4" />
        </div>
    </div>

</div>
@push('script')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('resolveIssue', ({ id }) => {
            Swal.fire({
                title: '¿Marcar este pendiente como resuelto?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('resolve', id);
                }
            });
        });

        Livewire.on('resolved', () => {
            Swal.fire({ icon: 'success', title: 'Pendiente resuelto', showConfirmButton: false, timer: 1500 });
        });
    });
</script>
@endpush
