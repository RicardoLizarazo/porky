<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\TypeOrder;
use App\Models\StatusOrder;
use App\Models\User;
use App\Models\KitchenOrderDetail;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Orders extends Component
{
    public $order_id;

    public $customer_id;
    public $type_id;
    public $status_id;
    public $payment_method;
    public $comment;
    public $indication;
    public $delivery_user_id;

    public $category_id = null;

    public $order;
    public $items = [];

    public $customer_name;
    public $customer_phone;
    public $customer_address;
    public $type_name;
    public $status_name;
    public $status_color;
    public $delivery_name;
    public $ordered_at;

    public $details = [];

    public $subtotal = 0;
    public $total = 0;
    public $delivery_cost = 9000;
    public $packaging_total = 0;
    
    public $is_table_order = false;
    public $table_name;
    public $floor_name;
    public $waiter_name;
    public $payments = [];
    public $tip = 0;
    public $is_paid = false;
    public $cancelledItems = [];

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function mount()
    {
        if (!Auth::user()?->can('users.view')) {
            abort(403, 'No tienes permiso para acceder a esta secci¨®n.');
        }

        $this->dispatch('check-order-focus');
    }

    public function render()
    {
        return view('orders.index', [
            'customers' => Customer::pluck('name', 'id'),
            'categories' => Category::all(),
            'products' => Product::with('category')->where('is_active', true)->get()->groupBy('category.name'),
            'statuses' => StatusOrder::pluck('name', 'id'),
            'types' => TypeOrder::pluck('name', 'id'),
            'deliveryUsers' => User::pluck('name', 'id'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    */

    public function resetInput()
    {
        $this->reset([
            'order_id',
            'customer_id',
            'type_id',
            'status_id',
            'payment_method',
            'comment',
            'indication',
            'items',
            'order'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    #[On('create')]
    public function create()
    {
        $this->resetInput();
        $this->dispatch('open-create-modal');
    }

    /*
    |--------------------------------------------------------------------------
    |  VER PEDIDO (FACTURA)
    |--------------------------------------------------------------------------
    */

    #[On('view-order')]
    public function viewOrder($id)
    {
        $order = Order::with([
            'customer.defaultAddress',
            'details',
            'status',
            'type',
            'delivery',
            'user',          // mesero
            'diningTable',
            'floor',
            'payments',
        ])->findOrFail($id);
    
        $this->order_id = $order->id;
    
        $this->customer_name = $order->customer?->name;
        $this->customer_phone = $order->customer?->telephone;
        $this->customer_address = $order->customer?->full_address;
    
        $this->type_name = $order->type?->name;
        $this->status_name = $order->status_badge['name'];
        $this->status_color = $order->status_badge['color'];
    
        $this->payment_method = $order->payment_method;
        $this->delivery_name = $order->delivery_name;
        $this->ordered_at = optional($order->ordered_at)->format('d/m/Y H:i');
    
        $this->subtotal = $order->subtotal;
        $this->total = $order->total;
        $this->delivery_cost = $order->delivery_cost;
        $this->packaging_total = $order->packaging_total ?? 0;
    
        // CARGAR OBSERVACIONES
        $this->comment = $order->comment;
        $this->indication = $order->indication;
    
        // detalle tipo factura
        $this->details = $order->details->map(function ($item) {
            return [
                'name' => $item->product_name,
                'qty' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
                'comment' => $item->comment,
            ];
        })->toArray();
        
        // ---- INFO DE MESA ----
        $this->is_table_order = ! is_null($order->table_id);
        
        $this->table_name  = $order->diningTable?->name ?? ($order->table_id ? 'Mesa ' . $order->table_id : null);
        $this->floor_name  = $order->floor?->name;
        $this->waiter_name = $order->user?->name ?? 'Sin asignar';
        
        $this->tip     = $order->tip ?? 0;
        $this->is_paid = (bool) $order->is_paid;
        
        // ---- FORMAS DE PAGO REALES ----
        $this->payments = $order->payments->map(function ($p) {
            return [
                'method' => $p->payment_method,
                'amount' => $p->amount,
            ];
        })->toArray();

        // ---- PRODUCTOS CANCELADOS (ya enviados a cocina y luego
        // eliminados del pedido) ----
        // El OrderDetail original ya no existe (se borr¨®), as¨ª que la
        // ¨²nica fuente de este historial es kitchen_order_details.
        $this->cancelledItems = $this->is_table_order
            ? KitchenOrderDetail::whereHas('kitchenOrder', function ($q) use ($order) {
                    $q->where('order_id', $order->id);
                })
                ->where('status', 'cancelled')
                ->with('resolvedBy')
                ->orderByDesc('ready_at')
                ->get()
                ->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity'     => $item->quantity,
                        'subtotal'     => $item->subtotal,
                        'cancelled_by' => $item->resolvedBy?->name,
                        'cancelled_at' => $item->ready_at
                            ? \Carbon\Carbon::parse($item->ready_at)->format('d/m/Y h:i A')
                            : '',
                    ];
                })
                ->toArray()
            : [];
    
        $this->dispatch('open-view-modal');
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR PEDIDO
    |--------------------------------------------------------------------------
    */

    #[On('edit-order')]
    public function editOrder($id)
    {
        $order = Order::with('details')->findOrFail($id);

        $this->order_id = $order->id;
        $this->customer_id = $order->customer_id;
        $this->type_id = $order->type_id;
        $this->status_id = $order->status_id;
        $this->payment_method = $order->payment_method;
        $this->delivery_user_id = $order->delivery_user_id;
        $this->comment = $order->comment;
        $this->indication = $order->indication;
        $this->delivery_cost = $order->delivery_cost ?? 0;

        // Obtener productos
        $productIds = $order->details->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $items = [];
        foreach ($order->details as $detail) {
            $product = $products[$detail->product_id] ?? null;
            $items[$detail->product_id] = [
                'product_id'     => $detail->product_id,
                'name'           => $detail->product_name,
                'price'          => $detail->price,
                'packaging_cost' => $product?->packaging_cost ?? 0,
                'qty'            => $detail->quantity,
            ];
        }

        $this->items = $items;
        $this->calculateTotals();
        
        $this->dispatch('open-edit-modal');
    }

    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO
    |--------------------------------------------------------------------------
    */

    #[On('change-status')]
    public function changeStatus($id, $status)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'status_id' => $status
        ]);

        $this->dispatch('refreshDatatable');
        $this->dispatch('success', message: 'Estado actualizado correctamente');
    }

    /*
    |--------------------------------------------------------------------------
    |  ELIMINAR
    |--------------------------------------------------------------------------
    */

    #[On('confirmDeleteOrder')]
    public function deleteOrder($id)
    {
        Order::findOrFail($id)->delete();

        $this->dispatch('refreshDatatable');
        $this->dispatch('success', message: 'Pedido eliminado correctamente');
    }

    /*
    |--------------------------------------------------------------------------
    | MANEJO DE ITEMS
    |--------------------------------------------------------------------------
    */

    public function addProduct($productId)
    {
        $product = Product::findOrFail($productId);

        $items = $this->items;

        if (isset($items[$productId])) {
            $items[$productId]['qty']++;
        } else {
            $items[$productId] = [
                'product_id'     => $product->id,
                'name'           => $product->name,
                'price'          => (float) $product->price,
                'packaging_cost' => (float) ($product->packaging_cost ?? 0),
                'qty'            => 1,
            ];
        }

        $this->items = $items;
        $this->calculateTotals();
    }

    public function removeItem($productId)
    {
        $items = $this->items;
        
        if (isset($items[$productId])) {
            unset($items[$productId]);
        }
        
        $this->items = $items;
        $this->calculateTotals();
    }

    public function updateQty($productId, $qty)
    {
        $items = $this->items;

        if ($qty <= 0) {
            unset($items[$productId]);
        } else {
            // Asegurar que se mantienen todas las propiedades del item
            if (isset($items[$productId])) {
                $items[$productId]['qty'] = (int) $qty;
            }
        }

        $this->items = $items;
        $this->calculateTotals();
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULOS
    |--------------------------------------------------------------------------
    */
    public function calculateTotals()
    {
        $this->subtotal = collect($this->items)->sum(function ($item) {
            return (int)($item['price'] ?? 0) * (int)($item['qty'] ?? 0);
        });
        
        $this->packaging_total = collect($this->items)->sum(function ($item) {
            return ((int)($item['packaging_cost'] ?? 0)) * (int)($item['qty'] ?? 0);
        });
        
        $this->total = $this->subtotal + $this->packaging_total + ((int)($this->delivery_cost ?? 0));
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR PEDIDO
    |--------------------------------------------------------------------------
    */

    public function store()
    {
        if (empty($this->items)) {
            $this->dispatch('error', message: 'Debe agregar productos');
            return;
        }

        $this->calculateTotals();

        $order = Order::create([
            'customer_id'   => $this->customer_id,
            //'user_id'       => auth()->id(),
            'type_id'       => $this->type_id,
            'status_id'     => 1,
            'payment_method'=> $this->payment_method,
            'subtotal'      => $this->subtotal,
            'packaging_total' => $this->packaging_total,
            'delivery_cost'   => $this->delivery_cost,
            'total'         => $this->total,
            'total_items'   => count($this->items),
            'comment'       => $this->comment,
            'indication'    => $this->indication,
            'ordered_at'    => now(),
        ]);

        foreach ($this->items as $item) {
            $order->details()->create([
                'product_id'   => $item['product_id'],
                'product_name' => $item['name'],
                'price'        => $item['price'],
                'quantity'     => $item['qty'],
                'subtotal'     => $item['price'] * $item['qty'],
            ]);
        }

        $this->dispatch('store');
        $this->dispatch('refreshDatatable');
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR PEDIDO
    |--------------------------------------------------------------------------
    */

    public function update()
    {
        $order = Order::findOrFail($this->order_id);

        $this->calculateTotals();

        $order->update([
            'customer_id'     => $this->customer_id,
            'type_id'         => $this->type_id,
            'status_id'       => $this->status_id,
            'payment_method'  => $this->payment_method,
            'subtotal'        => $this->subtotal,
            'packaging_total' => $this->packaging_total,
            'delivery_cost'   => $this->delivery_cost,
            'total'           => $this->total,
            'total_items'     => count($this->items),
            'comment'         => $this->comment,
            'indication'      => $this->indication,
        ]);

        // Eliminar detalles existentes
        $order->details()->delete();

        // Crear nuevos detalles
        foreach ($this->items as $item) {
            $order->details()->create([
                'product_id'   => $item['product_id'],
                'product_name' => $item['name'],
                'price'        => $item['price'],
                'quantity'     => $item['qty'],
                'subtotal'     => $item['price'] * $item['qty'],
            ]);
        }

        $this->dispatch('update');
        $this->dispatch('refreshDatatable');
        $this->dispatch('success', message: 'Pedido actualizado correctamente');
    }

    public function updatedDeliveryCost($value)
    {
        $this->delivery_cost = (float) $value;
        $this->calculateTotals();
    }

    #[On('change-delivery')]
    public function changeDelivery($id, $delivery)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'delivery_user_id' => $delivery
        ]);

        $this->dispatch('refreshDatatable');
        $this->dispatch('success', message: 'Domiciliario actualizado');
    }

    #[On('change-payment')]
    public function changePayment($id, $payment)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'payment_method' => $payment
        ]);

        $this->dispatch('refreshDatatable');
        $this->dispatch('success', message: 'M¨¦todo de pago actualizado');
    }

    #[On('apply-new-filter')]
    public function applyNewFilter()
    {
        $statusNuevoId = \App\Models\StatusOrder::where('name', 'Pendiente')->value('id');

        $this->status_id = $statusNuevoId;

        $this->dispatch('refreshDatatable');
    }
}