<?php

namespace App\Livewire\Restaurante;

use Livewire\Component;
use App\Models\KitchenOrder;
use App\Models\KitchenOrderDetail;
use App\Models\KitchenStation;
use App\Livewire\Restaurante\Concerns\ResolvesKitchenStations;
use Illuminate\Support\Facades\Auth;

class KitchenDispatch extends Component
{
    use ResolvesKitchenStations;

    public $station;

    public function mount($station)
    {
        $this->station = $station;
    }

    /**
     * Marca como listos todos los productos de ESTA(S) estaci¨®n(es)
     * para el pedido indicado. Si con eso quedan todos los productos
     * del pedido (de todas las estaciones, no solo las de esta
     * pantalla) listos, el pedido completo pasa a 'ready'.
     */
    public function ready($orderId)
    {
        // Sin el permiso de ver todo, un mesero solo puede despachar
        // sus propios pedidos (evita que fuerce el wire:click sobre
        // el id de un pedido ajeno).
        if (! Auth::user()->can('kitchen_dispatch.view_all')) {

            $belongsToUser = KitchenOrder::where('id', $orderId)
                ->whereHas('order', function ($q) {
                    $q->where('user_id', Auth::id());
                })
                ->exists();

            abort_unless($belongsToUser, 403, 'Este pedido no te pertenece.');
        }

        $stationIds = $this->resolveStationIds($this->station);

        KitchenOrderDetail::where('kitchen_order_id', $orderId)
            ->whereIn('kitchen_station_id', $stationIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'ready',
                'ready_at' => now(),
                'resolved_by' => Auth::id(),
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

        // Sin el permiso, cada mesero ve ¨²nicamente sus propios pedidos.
        $canViewAll = Auth::user()->can('kitchen_dispatch.view_all');

        $query = KitchenOrderDetail::with([

            'station',

            'kitchenOrder.diningTable',

            'kitchenOrder.floor',

            'kitchenOrder.order.user',

        ])
        ->whereIn('kitchen_station_id', $stationIds)
        ->where('status', 'pending')
        ->whereHas('kitchenOrder', function ($query) {

            $query->where('status', 'pending');

        });

        if (! $canViewAll) {
            $query->whereHas('kitchenOrder.order', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $details = $query->get()
            ->sortBy(function ($item) {
                return $item->kitchenOrder->sent_at;
            })
            ->values();

        $tickets = $details->groupBy('kitchen_order_id');

        return view(
            'restaurante.kitchen.dispatch',
            compact('tickets', 'stationLabel', 'isCombined', 'switcher', 'canViewAll')
        );
    }
}