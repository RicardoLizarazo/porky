<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\Order;

class RoutePlanner extends Component
{
    public Collection $orders;
    public $selected = [];

    public function mount()
    {
        // 🔥 pedidos pendientes con coordenadas
        $this->orders = Order::with(['customer.defaultAddress'])
            ->whereHas('customer.defaultAddress', function ($q) {
                $q->whereNotNull('latitude')
                ->whereNotNull('longitude');
            })
            ->where('status_id', 1)
            ->get();
    }

    public function toggleOrder($id)
    {
        $order = $this->orders->firstWhere('id', $id);

        if (!$order->customer?->defaultAddress) {
            $this->dispatch('error', message: 'Cliente sin dirección válida');
            return;
        }

        if (in_array($id, $this->selected)) {
            $this->selected = array_diff($this->selected, [$id]);
        } else {
            $this->selected[] = $id;
        }

        $this->dispatch('update-route', orders: $this->getSelectedOrders());
    }

    public function getSelectedOrders()
    {
        return $this->orders
            ->whereIn('id', $this->selected)
            ->map(function ($o) {

                $address = $o->customer?->defaultAddress;

                if (!$address) return null;

                return [
                    'id' => $o->id,
                    'lat' => (float) $address->latitude,
                    'lng' => (float) $address->longitude,
                    'address' => $address->address,
                ];

            })
            ->filter() // 🔥 elimina nulls
            ->values();
    }

    public function generateRoute()
    {
        $this->dispatch('update-route', orders: $this->getSelectedOrders());
    }

    public function render()
    {
        return view('livewire.route-planner');
    }
}
