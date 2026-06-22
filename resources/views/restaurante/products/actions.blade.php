<div class="btn-group" role="group">
    @can('products.edit')
        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('edit', { id: {{ $product->id }} })"
            class="btn btn-cef btn-cef-edit"
            title="Editar"
            data-toggle="tooltip"
        >
            <i class="fas fa-edit"></i>
        </button>
    @endcan

    <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('product-rules', { id: {{ $product->id }} })"
        class="btn btn-cef btn-secondary"
        title="Reglas"
        data-toggle="tooltip"
    >
        <i class="fas fa-random"></i>
    </button>

    @can('products.delete')
        <button wire:click="$dispatch('delete', {{ $product->id }})"
            class="btn btn-cef btn-cef-delete"
            title="Eliminar"
            data-toggle="tooltip"
        >
            <i class="fas fa-trash-alt"></i>
        </button>
    @endcan
</div>