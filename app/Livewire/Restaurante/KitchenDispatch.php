<?php

namespace App\Livewire\Restaurante;

use Livewire\Component;
use App\Models\KitchenOrder;

class KitchenDispatch extends Component
{
    public function ready($ticketId)
    {
        KitchenOrder::findOrFail($ticketId)
            ->update([
                'status' => 'ready',
                'ready_at' => now(),
            ]);
    }

    public function render()
    {
        $tickets = KitchenOrder::with([
            'diningTable',
            'floor',
            'details',
            'order.user'
        ])
        ->where('status', 'pending')
        ->latest()
        ->get();

        return view(
            'restaurante.kitchen.dispatch',
            compact('tickets')
        );
    }
}