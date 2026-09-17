<div class="btn-group" role="group">
    @can('purchases.view')
        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('view', { id: {{ $purchase->id }} })"
            class="btn btn-cef btn-cef-view"
            title="Ver"
            data-toggle="tooltip"
        >
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @if($purchase->status === 'draft')
        @can('purchases.create')
            <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('edit', { id: {{ $purchase->id }} })"
                class="btn btn-cef btn-cef-edit"
                title="Editar"
                data-toggle="tooltip"
            >
                <i class="fas fa-edit"></i>
            </button>
        @endcan

        @can('purchases.confirm')
            <button wire:click="$dispatch('confirmPurchase', { id: {{ $purchase->id }} })"
                class="btn btn-cef btn-cef-create"
                title="Confirmar"
                data-toggle="tooltip"
            >
                <i class="fas fa-check"></i>
            </button>
        @endcan

        @can('purchases.delete')
            <button wire:click="$dispatch('delete', {{ $purchase->id }})"
                class="btn btn-cef btn-cef-delete"
                title="Eliminar"
                data-toggle="tooltip"
            >
                <i class="fas fa-trash-alt"></i>
            </button>
        @endcan
    @endif
</div>
