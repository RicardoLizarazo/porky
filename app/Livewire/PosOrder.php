<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\KitchenOrder;
use App\Models\KitchenOrderDetail;

class PosOrder extends Component
{
    /*
    |--------------------------------------------------------------------------
    | ORDER
    |--------------------------------------------------------------------------
    */

    public Order $order;

    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    public $search = '';

    public $category_id = null;

    /*
    |--------------------------------------------------------------------------
    | CARRITO
    |--------------------------------------------------------------------------
    */

    public $cart = [];

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(Order $order)
    {
        $this->order = $order;

        $this->loadCart();
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD CART
    |--------------------------------------------------------------------------
    */

    public function loadCart()
    {
        $this->cart = OrderDetail::with('product')

            ->where('order_id', $this->order->id)

            ->get()

            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | ADD PRODUCT
    |--------------------------------------------------------------------------
    */

    public function addProduct($productId)
    {
        $detail = OrderDetail::where([
            'order_id' => $this->order->id,
            'product_id' => $productId,
        ])->first();

        if ($detail) {

            $detail->quantity++;

            $price =
                $detail->manual_price
                ?? $detail->price;

            $detail->subtotal =
                $detail->quantity * $price;

            $detail->save();

        } else {

            $product = Product::findOrFail($productId);

            OrderDetail::create([

                'order_id' => $this->order->id,

                'product_id' => $product->id,

                'product_name' => $product->name,

                'quantity' => 1,

                'price' => $product->price,

                'subtotal' => $product->price,
            ]);
        }

        $this->refreshOrder();
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE PRODUCT
    |--------------------------------------------------------------------------
    */

    public function removeProduct($detailId)
    {
        $detail = OrderDetail::findOrFail($detailId);

        if ($detail->quantity > 1) {

            $detail->quantity--;

            $price =
                $detail->manual_price
                ?? $detail->price;

            $detail->subtotal =
                $detail->quantity * $price;

            $detail->save();

        } else {

            $detail->delete();
        }

        $this->refreshOrder();
    }

    public function deleteDetail($detailId)
    {
        OrderDetail::findOrFail($detailId)->delete();

        $this->refreshOrder();
    }

    public function refreshOrder()
    {
        $this->cart = OrderDetail::with('product')
            ->where('order_id', $this->order->id)
            ->get()
            ->toArray();

        $this->updateTotals();

        $this->order->refresh();
    }

    public function updateTotals()
    {
        $details = OrderDetail::with('product')
            ->where('order_id', $this->order->id)
            ->get();

        $subtotal = $details->sum('subtotal');

        $items = $details->sum('quantity');

        $packaging = $details->sum(function ($detail) {

            preg_match(
                '/EMPAQUE=(\d+)/',
                $detail->comment ?? '',
                $matches
            );

            $packagingQty = (int) ($matches[1] ?? 0);

            return ($detail->product?->packaging_cost ?? 0)
                * $packagingQty;
        });

        $this->order->update([
            'subtotal'        => $subtotal,
            'packaging_total' => $packaging,
            'total_items'     => $items,
            'total'           => $subtotal + $packaging,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER CATEGORY
    |--------------------------------------------------------------------------
    */

    public function filterCategory($categoryId)
    {
        $this->category_id = $categoryId;
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

    public function getTotalProperty()
    {
        $this->order->refresh();

        return $this->order->total;
    }

    /*
    |--------------------------------------------------------------------------
    | EMPAQUE
    |--------------------------------------------------------------------------
    */

    public function getPackagingQty($item)
    {
        preg_match(
            '/EMPAQUE=(\d+)/',
            $item['comment'] ?? '',
            $matches
        );

        return (int) ($matches[1] ?? 0);
    }

    public function increasePackaging($detailId)
    {
        $detail = OrderDetail::findOrFail($detailId);

        preg_match(
            '/EMPAQUE=(\d+)/',
            $detail->comment ?? '',
            $matches
        );

        $qty = (int) ($matches[1] ?? 0);

        if ($qty < $detail->quantity) {
            $qty++;
        }

        $detail->comment = "EMPAQUE={$qty}";

        $detail->save();

        $this->refreshOrder();
    }

    public function decreasePackaging($detailId)
    {
        $detail = OrderDetail::findOrFail($detailId);

        preg_match(
            '/EMPAQUE=(\d+)/',
            $detail->comment ?? '',
            $matches
        );

        $qty = (int) ($matches[1] ?? 0);

        $qty = max(0, $qty - 1);

        $detail->comment = "EMPAQUE={$qty}";

        $detail->save();

        $this->refreshOrder();
    }

    /*
    |--------------------------------------------------------------------------
    | NUEVO PRECIO
    |--------------------------------------------------------------------------
    */

    public function updateManualPrice($detailId, $value)
    {
        $detail = OrderDetail::findOrFail($detailId);

        $value = (float) $value;

        if ($value < 0) {
            $value = 0;
        }

        $detail->manual_price = $value;

        $detail->subtotal =
            $value * $detail->quantity;

        $detail->save();

        $this->refreshOrder();
    }

    /*
    |--------------------------------------------------------------------------
    | COCINA
    |--------------------------------------------------------------------------
    */
    public function sendToKitchen()
    {
        $details = OrderDetail::with([
            'product.category'
        ])
        ->where(
            'order_id',
            $this->order->id
        )
        ->where('sent_to_kitchen', false)
        ->get();

        if ($details->isEmpty()) {

            $this->dispatch(
                'swal',
                icon:'warning',
                title:'No hay productos pendientes'
            );

            return;
        }

        $ticket = KitchenOrder::create([

            'order_id' => $this->order->id,

            'floor_id' => $this->order->floor_id,

            'table_id' => $this->order->table_id,

            'sent_at' => now()
        ]);

        foreach ($details as $detail) {

            $stationId = $detail->product
            ?->category
            ?->kitchen_station_id;

            KitchenOrderDetail::create([

                'kitchen_order_id' => $ticket->id,

                'order_detail_id' => $detail->id,

                'kitchen_station_id' => $stationId,

                'product_name' => $detail->product_name,

                'quantity' => $detail->quantity,

                'price' => $detail->manual_price
                    ?? $detail->price,

                'subtotal' => $detail->subtotal,

                'comment' => $detail->comment,
            ]);

            $detail->update([
                'sent_to_kitchen' => true
            ]);
        }

        $this->dispatch('success');
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $products = Product::query()

            ->with('category')

            ->where('is_active', true)

            ->when($this->search, function ($query) {

                $query->where('name', 'like', '%' . $this->search . '%');
            })

            ->when($this->category_id, function ($query) {

                $query->where(
                    'category_id',
                    $this->category_id
                );
            })

            ->orderBy('name')

            ->paginate(20);

        return view('restaurante.pos.index', [

            'products' => $products,

            'categories' => Category::active()
                ->where('is_visible', true)
                ->orderBy('name')
                ->get(),
        ]);
    }
}