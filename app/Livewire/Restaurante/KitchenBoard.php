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

        // Agrupamos por pedido, igual que en el Despacho: una sola
        // tarjeta por mesa con todos sus productos de esta estación,
        // en vez de una tarjeta por cada producto individual.
        $tickets = $details->groupBy('kitchen_order_id');

        return view(
            'restaurante.kitchen.board',
            compact(
                'tickets',
                'stationLabel',
                'isCombined',
                'switcher'
            )
        );
    }
}