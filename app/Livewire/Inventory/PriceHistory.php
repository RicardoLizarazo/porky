<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceHistory extends Component
{
    public function mount()
    {
        if (!Auth::user()?->can('price_history.view')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function render()
    {
        $summary = DB::table('purchase_details as pd')
            ->join('purchases as p', 'p.id', '=', 'pd.purchase_id')
            ->join('inventory_items as ii', 'ii.id', '=', 'pd.inventory_item_id')
            ->join('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->where('p.status', 'confirmed')
            ->where('pd.base_quantity', '>', 0)
            ->selectRaw('ii.id as item_id, ii.name as item_name, s.name as supplier_name, (pd.subtotal / pd.base_quantity) as unit_cost_base, p.purchase_date')
            ->get()
            ->groupBy('item_id')
            ->map(fn($rows) => $rows->sortByDesc('unit_cost_base')->first())
            ->values();

        return view('inventory.price-history.index', [
            'summary' => $summary,
        ]);
    }
}
