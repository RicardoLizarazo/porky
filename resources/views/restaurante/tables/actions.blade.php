<div class="btn-group" role="group">

    @can('tables.edit')
        <button
            @click="
                window.dispatchEvent(
                    new CustomEvent('toggle-loading', {
                        detail: true
                    })
                );

                $dispatch('edit', {
                    id: {{ $table->id }}
                })
            "
            class="btn btn-cef btn-cef-edit"
            title="Editar"
            data-toggle="tooltip"
        >
            <i class="fas fa-edit"></i>
        </button>
    @endcan

    @can('tables.delete')
        <button
            wire:click="$dispatch('delete', {{ $table->id }})"
            class="btn btn-cef btn-cef-delete"
            title="Eliminar"
            data-toggle="tooltip"
        >
            <i class="fas fa-trash-alt"></i>
        </button>
    @endcan

</div>