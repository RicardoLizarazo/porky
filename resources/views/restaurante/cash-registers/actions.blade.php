<div class="btn-group" role="group">
    @can('cash-registers.edit')
        <button
            @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('edit', {id: {{ $cashRegister->id }}})"
            class="btn btn-cef btn-cef-edit"
            title="Editar"
            data-toggle="tooltip">
            <i class="fas fa-edit"></i>
        </button>
    @endcan

    @can('cash-registers.delete')
        <button
            wire:click="$dispatch('delete', {{ $cashRegister->id }})"
            class="btn btn-cef btn-cef-delete"
            title="Eliminar"
            data-toggle="tooltip">
            <i class="fas fa-trash-alt"></i>
        </button>
    @endcan
</div>