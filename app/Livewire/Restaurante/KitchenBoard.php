<?php

namespace App\Livewire\Restaurante;

use Livewire\Component;
use App\Models\KitchenOrderDetail;
use App\Models\KitchenStation;
use App\Livewire\Restaurante\Concerns\ResolvesKitchenStations;

class KitchenBoard extends Component
{
    use ResolvesKitchenStations;

    public $station;

    public function mount($station)
    {
        $this->station = $station;
    }

    public function render()
    {
        $stationIds = $this->resolveStationIds($this->station);

        $stationModels = KitchenStation::whereIn('id', $stationIds)->get();

        if ($stationModels->isEmpty()) {
            abort(404);
        }

        $stationLabel = $this->resolveStationLabel($stationIds, $stationModels);

        $isCombined = count($stationIds) > 1;

        $switcher = $this->buildStationSwitcher('kitchen.board', $stationIds);

        $details = KitchenOrderDetail::with([

            'station',

            'kitchenOrder.floor',

            'kitchenOrder.diningTable',

            'kitchenOrder.order.user'

        ])
        ->whereIn('kitchen_station_id', $stationIds)
        ->where('status', 'pending')
        ->whereHas('kitchenOrder', function ($query) {

            $query->where(
                'status',
                'pending'
            );

        })
        ->get()
        ->sortBy(function ($item) {
            return $item->kitchenOrder->sent_at;
        })
        ->values();

        return view(
            'restaurante.kitchen.board',
            compact(
                'details',
                'stationLabel',
                'isCombined',
                'switcher'
            )
        );
    }
}