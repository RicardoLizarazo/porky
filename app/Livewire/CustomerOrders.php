<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class CustomerOrders extends Component
{
    public $order_id;

    public $status_name;
    public $status_color;
    public $delivery_name;
    public $ordered_at;
    public $customer_address;

    public $details = [];

    public $total = 0;
    public $subtotal = 0;
    public $delivery_cost = 0;
    public $packaging_total = 0;

    protected $listeners = ['view-order' => 'view'];

    public function getCustomerId()
    {
        return Auth::guard('customer')->id();
    }

    public function render()
    {
        return view('customers.customer-orders');
    }

    #[On('view-order')]
    public function view($id)
    {
        $order = Order::with([
            'customer.defaultAddress',
            'details',
            'status',
            'delivery'
        ])->findOrFail($id);



        $this->order_id = $order->id;

        $this->status_name = $order->status_badge['name'];
        $this->status_color = $order->status_badge['color'];

        $this->delivery_name = $order->delivery_name;
        $this->ordered_at = optional($order->ordered_at)->format('d/m/Y H:i');

        $this->customer_address = $order->customer?->full_address;

        $this->subtotal = $order->subtotal;
        $this->delivery_cost = $order->delivery_cost;
        $this->packaging_total = $order->packaging_total ?? 0;
        $this->total = $order->total;

        $this->details = $order->details->map(fn($item) => [
            'name' => $item->product_name,
            'qty' => $item->quantity,
            'subtotal' => $item->subtotal,
        ])->toArray();

        $this->dispatch('open-customer-view-modal');
    }
}