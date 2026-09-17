<div class="btn-group" role="group">
    @can('purchase_returns.view')
        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true })); $dispatch('view', { id: {{ $purchaseReturn->id }} })"
            class="btn btn-cef btn-cef-view"
            title="Ver"
            data-toggle="tooltip"
        >
            <i class="fas fa-eye"></i>
        </button>
    @endcan
</div>
