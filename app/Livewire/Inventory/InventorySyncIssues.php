<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\InventorySyncIssue;
use Illuminate\Support\Facades\Auth;

class InventorySyncIssues extends Component
{
    public function mount()
    {
        if (!Auth::user()?->can('inventory_sync_issues.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function render()
    {
        return view('inventory.sync-issues.index');
    }

    public function resolve($id)
    {
        if (!Auth::user()?->can('inventory_sync_issues.resolve')) {
            return;
        }

        InventorySyncIssue::whereKey($id)->update([
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        $this->dispatch('resolved');
        $this->dispatch('refreshDatatable');
    }
}
