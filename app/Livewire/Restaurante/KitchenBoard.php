<?php

namespace App\Livewire\Restaurante;

use Livewire\Component;
use App\Models\KitchenOrderDetail;
use App\Models\KitchenStation;

class KitchenBoard extends Component
{
    public $station;

    public function mount($station)
    {
        $this->station = $station;
    }

    public function render()
    {
        $station = KitchenStation::findOrFail(
            $this->station
        );

        $details = KitchenOrderDetail::with([

            'station',

            'kitchenOrder.floor',

            'kitchenOrder.diningTable',

            'kitchenOrder.order.user'

        ])
        ->where(
            'kitchen_station_id',
            $this->station
        )
        ->whereHas('kitchenOrder', function ($query) {

            $query->where(
                'status',
                'pending'
            );

        })
        ->latest()
        ->get();

        return view(
            'restaurante.kitchen.board',
            compact(
                'details',
                'station'
            )
        );
    }
}