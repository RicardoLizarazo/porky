<div class="btn-group" role="group">
    @can('suppliers.edit')
        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('edit', { id: {{ $supplier->id }} })"
            class="btn btn-cef btn-cef-edit"
            title="Editar"
            data-toggle="tooltip"
        >
            <i class="fas fa-edit"></i>
        </button>
    @endcan

    @can('suppliers.delete')
        <button wire:click="$dispatch('delete', {{ $supplier->id }})"
            class="btn btn-cef btn-cef-delete"
            title="Eliminar"
            data-toggle="tooltip"
        >
            <i class="fas fa-trash-alt"></i>
        </button>
    @endcan
</div>
