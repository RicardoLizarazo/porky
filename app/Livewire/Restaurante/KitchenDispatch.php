<?php

namespace App\Livewire\Restaurante;

use Livewire\Component;
use App\Models\KitchenOrder;
use App\Models\KitchenOrderDetail;
use App\Models\KitchenStation;
use App\Livewire\Restaurante\Concerns\ResolvesKitchenStations;

class KitchenDispatch extends Component
{
    use ResolvesKitchenStations;

    public $station;

    public function mount($station)
    {
        $this->station = $station;
    }

    /**
     * Marca como listos todos los productos de ESTA(S) estaci贸n(es)
     * para el pedido indicado. Si con eso quedan todos los productos
     * del pedido (de todas las estaciones, no solo las de esta
     * pantalla) listos, el pedido completo pasa a 'ready'.
     */
    public function ready($orderId)
    {
        $stationIds = $this->resolveStationIds($this->station);

        KitchenOrderDetail::where('kitchen_order_id', $orderId)
            ->whereIn('kitchen_station_id', $stationIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'ready',
                'ready_at' => now(),
            ]);

        $pendingElsewhere = KitchenOrderDetail::where('kitchen_order_id', $orderId)
            ->where('status', '!=', 'ready')
            ->exists();

        if (! $pendingElsewhere) {
            KitchenOrder::where('id', $orderId)->update([
                'status' => 'ready',
                'ready_at' => now(),
            ]);
        }
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

        $switcher = $this->buildStationSwitcher('kitchen.dispatch', $stationIds);

        $details = KitchenOrderDetail::with([

            'station',

            'kitchenOrder.diningTable',

            'kitchenOrder.floor',

            'kitchenOrder.order.user',

        ])
        ->whereIn('kitchen_station_id', $stationIds)
        ->where('status', 'pending')
        ->whereHas('kitchenOrder', function ($query) {

            $query->where('status', 'pending');

        })
        ->get()
        ->sortBy(function ($item) {
            return $item->kitchenOrder->sent_at;
        })
        ->values();

        $tickets = $details->groupBy('kitchen_order_id');

        return view(
            'restaurante.kitchen.dispatch',
            compact('tickets', 'stationLabel', 'isCombined', 'switcher')
        );
    }
}