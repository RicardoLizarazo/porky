<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class Cart extends Component
{
    public $cart = [];
    public $customer_id;
    public $customers = [];
    public $open = false;

    public $indication = '';
    public $comment = '';

    public $delivery_cost = 9000;

    protected $listeners = [
        'cart-add' => 'add',
        'cart-remove' => 'remove',
        'open-cart' => 'open',
        'close-cart' => 'close',
        'execute-confirm-order' => 'confirmOrder',
    ];

    public function mount()
    {
        $this->cart = session()->get('cart', []);

        // ðŸ”¥ cargar clientes si es admin
        if (Auth::check() && !Auth::guard('customer')->check()) {
            $this->customers = \App\Models\Customer::select('id','name','telephone')->get();
        }
    }

    public function render()
    {
        return view('livewire.cart');
    }

    public function open()
    {
        $this->open = true;

        $this->dispatch('cart-opened');
    }

    public function close()
    {
        $this->open = false;

        $this->dispatch('cart-closed');
    }

    public function add($id)
    {
        $product = Product::findOrFail($id);

        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity']++;
        } else {
            $this->cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
                'packaging_cost' => $product->packaging_cost ?? 0
            ];
        }

        $this->sync();
        $this->dispatch('product-added-toast', name: $product->name);
    }

    public function remove($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity']--;

            if ($this->cart[$id]['quantity'] <= 0) {
                unset($this->cart[$id]);
            }
        }

        $this->sync();
    }

    private function sync()
    {
        session()->put('cart', $this->cart);
        $this->dispatch('cart-updated');
        //$this->dispatch('open-cart');
    }

    public function getSelectedCustomerProperty()
    {
        return $this->customer_id
            ? \App\Models\Customer::find($this->customer_id)
            : null;
    }

    #[On('customerSelected')]
    public function customerSelected($id)
    {
        $this->customer_id = $id;
    }

    public function getTotalProperty()
    {
        $subtotal = $this->subtotal;

        $packaging = $this->packagingTotal;

        $delivery = $this->delivery_cost ?? 0;

        return $subtotal + $packaging + $delivery;
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)
            ->sum(fn($i) => $i['price'] * $i['quantity']);
    }

    public function getPackagingTotalProperty()
    {
        return collect($this->cart)
            ->sum(fn($i) => ($i['packaging_cost'] ?? 0) * $i['quantity']);
    }

    public function confirm()
    {
        if (empty($this->cart)) {
            $this->dispatch('order-error', 'El carrito estÃ¡ vacÃ­o');
            return;
        }

        if (Auth::check() && !Auth::guard('customer')->check() && !$this->customer_id) {
            $this->dispatch('order-error', 'Debe seleccionar un cliente');
            return;
        }

        // ðŸ”¥ dispara swal
        $this->dispatch('confirm-order');
    }

    //public function cancelConfirm()
    //{
        //$this->confirmingOrder = false;
    //}

    /*
    |--------------------------------------------------------------------------
    | CONFIRMAR ORDEN
    |--------------------------------------------------------------------------
    */
    public function confirmOrder()
    {
        try {
            DB::transaction(function () {

                $customerId = $this->resolveCustomerId();

                if (empty($this->cart)) {
                    throw new \Exception('El carrito estÃ¡ vacÃ­o');
                }

                $totalItems = collect($this->cart)->sum('quantity');

                $subtotal = collect($this->cart)
                    ->sum(fn($i) => $i['price'] * $i['quantity']);

                $packaging = collect($this->cart)
                    ->sum(fn($i) => ($i['packaging_cost'] ?? 0) * $i['quantity']);

                $delivery = $this->delivery_cost ?? 0;

                // ðŸ”¥ TOTAL CORRECTO
                $total = $subtotal + $packaging + $delivery;
                
                // 🔥 Tipo de pedido
                $typeId = 1; // Web

                if (Auth::check() && Auth::user()->hasAnyRole(['Administrador', 'Consulta'])) {
                    $typeId = 2; // Teléfono
                }

                $order = Order::create([
                    'customer_id'    => $customerId,
                    'user_id'        => Auth::id(),
                    'type_id'        => $typeId,
                    'status_id'      => 1,
                    'payment_method' => 'Efectivo',
                    'total_items'    => $totalItems,
                    'subtotal'       => $subtotal,
                    'packaging_total'=> $packaging,
                    'total'          => $total,
                    'delivery_cost'  => $delivery,
                    'indication'     => $this->indication,
                    'comment'        => $this->comment,
                    'ordered_at'     => now(),
                ]);

                foreach ($this->cart as $item) {
                    OrderDetail::create([
                        'order_id'     => $order->id,
                        'product_id'   => $item['id'],
                        'product_name' => $item['name'],
                        'quantity'     => $item['quantity'],
                        'price'        => $item['price'],
                        'subtotal'     => $item['price'] * $item['quantity'],
                    ]);
                }

                // ðŸ”¥ limpiar carrito BIEN
                session()->forget('cart');

                $this->reset([
                    'cart',
                    'customer_id',
                    'indication',
                    'comment',
                    'delivery_cost'
                ]);

                $this->delivery_cost = 9000;

                // ðŸ”¥ cerrar carrito
                $this->close();

                $this->dispatch('cart-updated');

                $this->dispatch('order-success', $order->id);

                $this->dispatch('print-ticket', url: route('orders.ticket', $order->id));

            });

        } catch (\Exception $e) {
            $this->dispatch('order-error', $e->getMessage());
        }
    }

    private function resolveCustomerId()
    {
        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->id();
        }

        if (Auth::check()) {
            if (!$this->customer_id) {
                throw new \Exception('Debe seleccionar un cliente');
            }

            return $this->customer_id;
        }

        throw new \Exception('Debe iniciar sesiÃ³n');
    }
}