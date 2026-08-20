<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\KitchenOrder;
use App\Models\KitchenOrderDetail;
use App\Models\DiningTable;
use Illuminate\Support\Facades\Hash;

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
        // Solo reutilizamos una fila existente si todavía NO se envió a
        // cocina. Si ya se envió, las unidades nuevas van en una fila
        // aparte, para no mezclar lo ya preparado con lo pendiente.
        $detail = OrderDetail::where([
            'order_id' => $this->order->id,
            'product_id' => $productId,
            'sent_to_kitchen' => false,
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
        $details = OrderDetail::with('product.kitchenStations')
            ->where('order_id', $this->order->id)
            ->where('sent_to_kitchen', false)
            ->get();

        if ($details->isEmpty()) {

            $this->dispatch(
                'swal',
                icon: 'warning',
                title: 'No hay productos pendientes'
            );

            return;
        }

        // En el piso Vitrina, los productos de la estación Picada se
        // entregan de una vez al pedirlos (no hay espera de cocina),
        // así que no deben generar ticket en ningún board/despacho.
        $isVitrinaFloor = strtolower($this->order->floor?->name ?? '') === 'vitrina';

        $kitchenStationsFor = function ($detail) use ($isVitrinaFloor) {

            $stations = $detail->product?->kitchenStations ?? collect();

            if ($isVitrinaFloor) {
                $stations = $stations->reject(
                    fn ($station) => strtolower($station->name) === 'picadas'
                );
            }

            return $stations;
        };

        // Solo los productos que requieren cocina (tienen al menos una
        // estación asignada, descontando la excepción de arriba)
        $kitchenDetails = $details->filter(function ($detail) use ($kitchenStationsFor) {
            return $kitchenStationsFor($detail)->isNotEmpty();
        });

        // Si ningún producto pendiente requiere preparación
        if ($kitchenDetails->isEmpty()) {

            foreach ($details as $detail) {
                $detail->update([
                    'sent_to_kitchen' => true,
                ]);
            }

            $this->dispatch(
                'swal',
                icon: 'info',
                title: 'Este pedido no contiene productos para preparación.'
            );

            return;
        }

        $ticket = KitchenOrder::create([
            'order_id' => $this->order->id,
            'floor_id' => $this->order->floor_id,
            'table_id' => $this->order->table_id,
            'sent_at'  => now(),
        ]);

        foreach ($details as $detail) {

            $stations = $kitchenStationsFor($detail);

            // Productos que no pasan por cocina, o cuya única estación
            // quedó descartada por la excepción de Vitrina + Picada
            if ($stations->isEmpty()) {

                $detail->update([
                    'sent_to_kitchen' => true,
                ]);

                continue;
            }

            $unitPrice = $detail->manual_price ?? $detail->price;

            // Una fila por cada estación asignada al producto: una picada
            // que va a Parrilla y a Picadas genera 2 tickets, uno en cada
            // board/despacho, cada uno marcándose listo por separado.
            foreach ($stations as $station) {

                KitchenOrderDetail::create([
                    'kitchen_order_id'   => $ticket->id,
                    'order_detail_id'    => $detail->id,
                    'kitchen_station_id' => $station->id,
                    'product_name'       => $detail->product_name,
                    'quantity'           => $detail->quantity,
                    'price'              => $unitPrice,
                    'subtotal'           => $detail->subtotal,
                    'comment'            => $detail->comment,
                ]);
            }

            $detail->update([
                'sent_to_kitchen' => true,
            ]);
        }

        $this->dispatch('success');
    }


    /**
     * IDs de order_details que YA fueron enviados a cocina para esta orden.
     * Se usa en la vista para mostrar el badge "Enviado a cocina" y bloquear
     * el borrado directo de esos items.
     */
    public function getSentToKitchenIdsProperty()
    {
        return KitchenOrderDetail::whereHas('kitchenOrder', function ($q) {
                $q->where('order_id', $this->order->id);
            })
            ->pluck('order_detail_id')
            ->unique()
            ->toArray();
    }
    
/**
     * Vuelve a leer la orden desde la BD (por ejemplo, is_paid) sin recargar
     * nada más. La usa el wire:poll de las estaciones de Vitrina, para que
     * el botón "Nuevo cliente" se active solo apenas caja cobra, sin que el
     * picador tenga que tocar la pantalla para "despertar" el refresco.
     */
    public function refreshOrderStatus(): void
    {
        if (($this->order->floor?->name ?? '') !== 'Vitrina') {
            return;
        }

        // Consulta directa: evita el modelo cacheado en memoria.
        // La columna real es status_id (no "status", que es el nombre
        // de la relación en el modelo, no una columna de la tabla).
        $estado = \DB::table('orders')
            ->where('id', $this->order->id)
            ->first(['is_paid', 'status_id']);

        // Si desapareció, se anuló o ya se cobró -> liberar la estación.
        // Order::STATUS_PAID no existe como constante; is_paid ya cubre
        // ese caso, así que solo se agrega STATUS_CANCELLED aparte.
        $liberar = ! $estado
            || (bool) $estado->is_paid
            || (int) $estado->status_id === Order::STATUS_CANCELLED;

        if (! $liberar) {
            return;
        }

        preg_match('/(\d+)$/', $this->order->diningTable?->name ?? '', $m);

        if (! isset($m[1])) {
            return;
        }

        session()->flash('vitrina_liberada', true);

        // navigate: false -> recarga completa, DOM limpio para el siguiente cliente
        $this->redirectRoute('pos.vitrina', $m[1], navigate: false);
    }


    /**
     * Elimina un producto del carrito que YA fue enviado a cocina.
     * Requiere el PIN de autorización (config('pos.removal_pin')), que es
     * independiente de cualquier contraseña de cuenta — lo puede escribir
     * quien esté frente al terminal en ese momento, sin necesidad de cerrar
     * la sesión del mesero para que un admin inicie la suya.
     * Queda registrado con Spatie Activitylog contra el usuario que tiene
     * la sesión abierta (quien "causó" la acción en el sistema).
     */
    public function removeProductSecure($itemId, $pin, $reason)
    {
        if (!hash_equals((string) config('pos.removal_pin'), (string) $pin)) {
            $this->dispatch('show-error', message: 'PIN de autorización incorrecto.');
            return;
        }
    
        $user = auth()->user();
    
        $motivos = [
            'cliente_cancelo'  => 'Cliente canceló',
            'error_pedido'     => 'Error al tomar el pedido',
            'producto_agotado' => 'Producto agotado en cocina',
            'otro'             => 'Otro motivo',
        ];
    
        $motivoTexto = $motivos[$reason] ?? $reason;
    
        $detail = OrderDetail::find($itemId);
    
        activity('order')
            ->causedBy($user)
            ->performedOn($this->order)
            ->withProperties([
                'order_detail_id' => $itemId,
                'producto'        => $detail?->product_name,
                'cantidad'        => $detail?->quantity,
                'motivo'          => $motivoTexto,
            ])
            ->log('Producto quitado del pedido (ya enviado a cocina)');
    
        $this->deleteDetail($itemId);
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user && $user->hasAnyRole(['Administrador', 'Mesero', 'Cajero']);

        // CATEGORÍAS
        $categories = Category::query()
            ->where('is_active', true)
            ->when(
                !$isAdmin,
                fn ($q) => $q->where('is_visible', true)
            )
            ->orderBy('name')
            ->get();

        // PRODUCTOS
        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            // Visibilidad por rol
            ->when(
                !$isAdmin,
                fn ($q) => $q->where('is_visible', true)
            )
            // Asegura coherencia con categoría
            ->whereHas('category', function ($q) use ($isAdmin) {
                $q->where('is_active', true);
                if (!$isAdmin) {
                    $q->where('is_visible', true);
                }
            })
            // Filtro por categoría
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            // Búsqueda
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(20);

        return view('restaurante.pos.index', [
            'products' => $products,
            'categories' => $categories,
            'sentToKitchenIds' => $this->sentToKitchenIds,
        ]);
    }
}