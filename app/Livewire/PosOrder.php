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
    | OPCIONES DE PRODUCTO
    |--------------------------------------------------------------------------
    | El texto elegido se guarda en order_details.comment, igual que el
    | empaque. Formato del campo:  "Sin Dulce, Con hielo | EMPAQUE=2"
    */

    public $showOptionsModal = false;

    public $optionsProductId = null;

    public $optionsProductName = '';

    public $optionsList = [];

    public $optionsSelected = null;

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
    | HELPERS DEL CAMPO COMMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Devuelve solo la parte de opciones del comment, sin el token de empaque
     * ni el de nota para cocina.
     */
    protected function optionsFromComment(?string $comment): string
    {
        $text = (string) $comment;

        $text = preg_replace('/\s*\|?\s*EMPAQUE=\d+/', '', $text);

        $text = preg_replace('/\s*\|?\s*NOTA=.*$/s', '', $text);

        return trim($text);
    }

    /**
     * Devuelve solo el texto libre que el mesero escribió como nota para
     * cocina (token NOTA=... al final del comment).
     */
    protected function noteFromComment(?string $comment): string
    {
        preg_match('/NOTA=(.*)$/s', (string) $comment, $matches);

        return trim($matches[1] ?? '');
    }

    /**
     * Reconstruye el comment conservando las 3 partes que puede tener:
     * opciones elegidas, empaque y nota libre del mesero para cocina.
     * Se usa siempre que se toque cualquiera de las tres, para no borrar
     * las otras dos.
     */
    protected function buildComment(?string $options, int $packaging, ?string $note = null): ?string
    {
        $parts = [];

        if (filled($options)) {
            $parts[] = trim($options);
        }

        if ($packaging > 0) {
            $parts[] = "EMPAQUE={$packaging}";
        }

        if (filled($note)) {
            $parts[] = "NOTA=" . trim($note);
        }

        return $parts ? implode(' | ', $parts) : null;
    }

    /**
     * Texto legible para la pantalla de cocina: opciones + para llevar +
     * nota libre del mesero, sin los tokens tecnicos EMPAQUE=/NOTA=.
     */
    protected function kitchenNote(?string $comment): ?string
    {
        $options = $this->optionsFromComment($comment);

        $note = $this->noteFromComment($comment);

        preg_match('/EMPAQUE=(\d+)/', (string) $comment, $matches);

        $packaging = (int) ($matches[1] ?? 0);

        $parts = [];

        if (filled($options)) {
            $parts[] = $options;
        }

        if ($packaging > 0) {
            $parts[] = "Para llevar: {$packaging}";
        }

        if (filled($note)) {
            $parts[] = "Nota: {$note}";
        }

        return $parts ? implode(' | ', $parts) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | ADD PRODUCT
    |--------------------------------------------------------------------------
    */

    /**
     * $options === null  -> hay que preguntar (abre el modal si aplica)
     * $options === ''    -> ya se resolvio, sin opciones
     * $options === '...' -> ya se resolvio, con opciones
     */
    public function addProduct($productId, ?string $options = null)
    {
        $product = Product::with('options')->findOrFail($productId);

        // Producto con opciones configuradas y todavia sin resolver
        if ($options === null && $product->options->isNotEmpty()) {

            $this->optionsProductId   = $product->id;
            $this->optionsProductName = $product->name;
            $this->optionsList        = $product->options->pluck('name')->all();
            $this->optionsSelected    = null;
            $this->showOptionsModal   = true;

            return;
        }

        $options = (string) $options;

        // Solo reutilizamos una fila existente si todavía NO se envió a
        // cocina Y si tiene exactamente las mismas opciones. Si ya se envió,
        // las unidades nuevas van en una fila aparte, para no mezclar lo ya
        // preparado con lo pendiente.
        $detail = OrderDetail::where('order_id', $this->order->id)
            ->where('product_id', $productId)
            ->where('sent_to_kitchen', false)
            ->get()
            ->first(fn ($row) => $this->optionsFromComment($row->comment) === $options);

        if ($detail) {

            $detail->quantity++;

            $price =
                $detail->manual_price
                ?? $detail->price;

            $detail->subtotal =
                $detail->quantity * $price;

            $detail->save();

        } else {

            OrderDetail::create([

                'order_id' => $this->order->id,

                'product_id' => $product->id,

                'product_name' => $product->name,

                'quantity' => 1,

                'price' => $product->price,

                'subtotal' => $product->price,

                'comment' => $this->buildComment($options, 0),
            ]);
        }

        $this->refreshOrder();
    }

    /**
     * Boton "+" del carrito. Repite la MISMA linea (mismas opciones) en vez
     * de volver a preguntar.
     */
    public function increaseDetail($detailId)
    {
        $detail = OrderDetail::findOrFail($detailId);

        $this->addProduct(
            $detail->product_id,
            $this->optionsFromComment($detail->comment)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MODAL DE OPCIONES
    |--------------------------------------------------------------------------
    */

    public function toggleOptionValue($name)
    {
        $this->optionsSelected = ($this->optionsSelected === $name) ? null : $name;
    }

    public function confirmOptions()
    {
        $productId = $this->optionsProductId;

        $options = (string) $this->optionsSelected;

        $this->closeOptionsModal();

        $this->addProduct($productId, $options);
    }

    public function closeOptionsModal()
    {
        $this->showOptionsModal   = false;
        $this->optionsProductId   = null;
        $this->optionsProductName = '';
        $this->optionsList        = [];
        $this->optionsSelected = null;
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
            
            $this->cancelKitchenTicketsFor($detail);

            $detail->delete();
        }

        $this->refreshOrder();
    }

    public function deleteDetail($detailId)
    {
        $detail = OrderDetail::findOrFail($detailId);

        $this->cancelKitchenTicketsFor($detail);

        $detail->delete();

        $this->refreshOrder();
    }
    
    /**
     * Cancela en cascada los tickets de cocina de un OrderDetail que ya
     * se había enviado (si nunca se envió, esto simplemente no encuentra
     * nada y no hace nada). Un producto que ya se marcó "listo" en su
     * estación NO se toca — se respeta lo que cocina ya preparó.
     *
     * Si al cancelar no queda ningún producto pendiente en el pedido de
     * cocina (todo quedó ready o cancelled), el pedido completo pasa a
     * 'ready' para que deje de aparecer como pendiente en cualquier
     * board/despacho, igual que ya pasa cuando se despacha normalmente.
     */
    private function cancelKitchenTicketsFor(OrderDetail $detail): void
    {
        $affectedOrderIds = KitchenOrderDetail::where('order_detail_id', $detail->id)
            ->where('status', 'pending')
            ->pluck('kitchen_order_id')
            ->unique();

        if ($affectedOrderIds->isEmpty()) {
            return;
        }

        KitchenOrderDetail::where('order_detail_id', $detail->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'cancelled',
                'ready_at' => now(),
                'resolved_by' => Auth::id(),
            ]);

        foreach ($affectedOrderIds as $kitchenOrderId) {

            $stillPending = KitchenOrderDetail::where('kitchen_order_id', $kitchenOrderId)
                ->where('status', 'pending')
                ->exists();

            if (! $stillPending) {
                KitchenOrder::where('id', $kitchenOrderId)->update([
                    'status' => 'ready',
                    'ready_at' => now(),
                ]);
            }
        }
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

        // Reconstruye conservando las opciones y la nota del mesero
        $detail->comment = $this->buildComment(
            $this->optionsFromComment($detail->comment),
            $qty,
            $this->noteFromComment($detail->comment)
        );

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

        // Reconstruye conservando las opciones y la nota del mesero
        $detail->comment = $this->buildComment(
            $this->optionsFromComment($detail->comment),
            $qty,
            $this->noteFromComment($detail->comment)
        );

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
    | NOTA PARA COCINA
    |--------------------------------------------------------------------------
    | Texto libre que el mesero escribe por item del carrito (ej. "sin
    | cebolla", "extra picante"). Se guarda dentro del mismo campo comment,
    | como token NOTA=..., preservando las opciones y el empaque ya
    | elegidos. No requiere reenviar a cocina para guardarse, pero si el
    | item ya fue enviado, el ticket original en KitchenOrderDetail no se
    | actualiza retroactivamente — solo aplica a partir del próximo envío.
    */

    public function updateItemNote($detailId, $value)
    {
        $detail = OrderDetail::findOrFail($detailId);

        $note = trim((string) $value);

        if (mb_strlen($note) > 120) {
            $note = mb_substr($note, 0, 120);
        }

        preg_match(
            '/EMPAQUE=(\d+)/',
            $detail->comment ?? '',
            $matches
        );

        $packaging = (int) ($matches[1] ?? 0);

        $detail->comment = $this->buildComment(
            $this->optionsFromComment($detail->comment),
            $packaging,
            $note
        );

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

            // El mesero cambió el precio manualmente (ej. media porción de
            // Chunchullo/Rellena). En vez de una columna nueva, se codifica
            // en el comment que viaja a cocina, igual patrón que EMPAQUE=N.
            $kitchenComment = $detail->comment;

            if (! is_null($detail->manual_price)) {
                $kitchenComment = 'PRECIO_AJUSTADO=' . (int) $unitPrice
                    . ($kitchenComment ? '|' . $kitchenComment : '');
            }

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
                    'comment'            => $kitchenComment,
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