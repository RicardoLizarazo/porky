<div class="btn-group" role="group">
    @can('inventory_sync_issues.resolve')
        <button wire:click="$dispatch('resolveIssue', { id: {{ $issue->id }} })"
            class="btn btn-cef btn-cef-create"
            title="Marcar resuelto"
            data-toggle="tooltip"
        >
            <i class="fas fa-check"></i> Marcar resuelto
        </button>
    @endcan
</div>
