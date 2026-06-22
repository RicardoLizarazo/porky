<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\StatusOrder;
use Livewire\Attributes\On;

class OrdersBell extends Component
{
    public $orders = [];
    public $count = 0;
    public $lastCount = 0;

    public $statusNuevoId;

    // ✅ SOLO PEDIDOS WEB
    public $typeWebId = 1;

    public function mount()
    {
        $this->statusNuevoId = StatusOrder::where('name', 'Pendiente')
            ->value('id');

        $this->loadOrders();

        $this->lastCount = $this->count;
    }

    public function loadOrders()
    {
        $query = Order::where('status_id', $this->statusNuevoId)
            ->where('type_id', $this->typeWebId);

        $this->orders = $query
            ->latest()
            ->take(5)
            ->get();

        $this->count = $query->count();
    }

    #[On('check-orders')]
    public function checkOrders()
    {
        $newCount = Order::where('status_id', $this->statusNuevoId)
            ->where('type_id', $this->typeWebId)
            ->count();

        if ($newCount > $this->lastCount) {

            $diff = $newCount - $this->lastCount;

            $this->js("
                window.dispatchEvent(new CustomEvent('new-order', {
                    detail: { diff: $diff }
                }));
            ");
        }

        $this->lastCount = $newCount;

        $this->loadOrders();
    }

    public function render()
    {
        return view('livewire.orders-bell');
    }
}