<?php

namespace App\Livewire\Restaurante;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KitchenOrderDetail;
use App\Models\KitchenStation;
use Illuminate\Pagination\LengthAwarePaginator;

class KitchenDispatchHistory extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $stationFilter = '';
    public $statusFilter = '';
    public $orderSearch = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfDay()->format('Y-m-d');
        $this->dateTo = now()->endOfDay()->format('Y-m-d');
    }

    public function updated($property)
    {
        if (in_array($property, ['dateFrom', 'dateTo', 'stationFilter', 'statusFilter', 'orderSearch'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = KitchenOrderDetail::with([
            'station',
            'resolvedBy',
            'kitchenOrder.diningTable',
            'kitchenOrder.floor',
        ])
        ->whereIn('status', ['ready', 'cancelled'])
        ->whereNotNull('ready_at');

        if ($this->orderSearch !== '') {
            // Buscando por n¨²mero de pedido: no tiene sentido acotar
            // tambi¨¦n por fecha, ya que justo es para encontrarlo sin
            // saber cu¨¢ndo se despach¨®.
            $query->whereHas('kitchenOrder', function ($q) {
                $q->where('order_id', $this->orderSearch);
            });
        } else {
            $query->whereDate('ready_at', '>=', $this->dateFrom)
                ->whereDate('ready_at', '<=', $this->dateTo);
        }

        if ($this->stationFilter !== '') {
            $query->where('kitchen_station_id', $this->stationFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $details = $query->orderByDesc('ready_at')->get();

        // Un despacho puede resolver varios productos a la vez (todo lo
        // pendiente de una mesa en esa estaci¨®n, en un solo clic). Se
        // agrupan como un solo "evento" cuando comparten pedido +
        // estaci¨®n + momento exacto + usuario.
        $events = $details
            ->groupBy(function ($item) {
                return $item->kitchen_order_id . '|'
                    . $item->kitchen_station_id . '|'
                    . $item->ready_at . '|'
                    . $item->resolved_by;
            })
            ->values();

        $perPage = 20;
        $page = $this->getPage();

        $paginator = new LengthAwarePaginator(
            $events->forPage($page, $perPage),
            $events->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('restaurante.kitchen.dispatch-history', [
            'events' => $paginator,
            'stations' => KitchenStation::active()->orderBy('name')->get(),
        ]);
    }
}