<div class="btn-group" role="group">
    @can('product-rules.edit')
        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('edit', { id: {{ $rule->id }} })"
            class="btn btn-cef btn-cef-edit"
            title="Editar"
            data-toggle="tooltip"
        >
            <i class="fas fa-edit"></i>
        </button>
    @endcan

    @can('product-rules.delete')
        <button
            class="btn btn-cef btn-cef-delete"
            wire:click="$dispatch('delete', {{ $rule->id }})"
            title="Eliminar"
        >
            <i class="fas fa-trash-alt"></i>
        </button>
    @endcan
</div>