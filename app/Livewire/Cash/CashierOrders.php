<?php

namespace App\Livewire\Cash;

use Livewire\Component;
use App\Models\Order;
use App\Models\CashSession;
use App\Models\CashPayment;
use App\Models\DiningTable;
use App\Models\Customer;
use App\Models\OrderCancellation;
use App\Services\Inventory\SalesInventorySync;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class CashierOrders extends Component
{
    public $order;

    // ID de la CashSession que el usuario tiene activa/seleccionada en las pestañas
    public $activeSessionId = null;

    // ID de la CashSession a la que pertenecía la orden en el momento de abrir el modal de pago.
    // Se congela aquí para que un cambio de pestaña mientras el modal está abierto no cobre
    // la orden contra la caja equivocada.
    public $orderSessionId = null;

    public $payments = [];

    public $invoice_requested = false;

    protected $rules = [
        'payments.*.payment_method' => 'required',
        'payments.*.amount' => 'required|numeric|min:1',
    ];

    public $invoice = [
        'document_type' => 'CC',
        'document_number' => '',
        'customer_name' => '',
        'company_name' => '',
        'email' => '',
        'phone' => '',
        'address' => '',
        'city' => '',
    ];
    
    public $isProcessingPayment = false;

    /*
    |--------------------------------------------------------------------------
    | SESIONES DE CAJA ABIERTAS (un usuario puede tener varias, ej. Salón
    | Principal, Salón Rojo y Salón Blanco en el 2do piso)
    |--------------------------------------------------------------------------
    */

    protected function openSessions()
    {
        return CashSession::query()

            ->with('cashRegister')

            ->open()

            ->where('user_id', auth()->id())

            ->get();
    }

    public function selectSession($sessionId)
    {
        $this->activeSessionId = $sessionId;
    }

    /*
    |--------------------------------------------------------------------------
    | MODAL
    |--------------------------------------------------------------------------
    */

    public function openPaymentModal($orderId)
    {
        // Se congela la sesión activa en el momento de abrir el modal, para
        // que el pago se aplique a la caja correcta aunque el usuario cambie
        // de pestaña mientras cobra.
        $this->isProcessingPayment = false;
        
        $this->orderSessionId = $this->activeSessionId;

        $this->invoice_requested = false;

        $this->invoice = [
            'document_type' => 'CC',
            'document_number' => '',
            'customer_name' => '',
            'company_name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'city' => '',
        ];

        $this->order = Order::with([
            'details.product',
            'diningTable',
            'user',
            'floor',
        ])->findOrFail($orderId);

        $this->invoice_requested = false;

        $this->payments = [
            [
                'payment_method' => 'Efectivo',
                'amount' => $this->order->total,
            ]
        ];

        $this->dispatch('open-payment-modal');
    }

    public function getCashReceivedProperty()
    {
        return collect($this->payments)
            ->where('payment_method', 'Efectivo')
            ->sum(function ($payment) {
                return is_numeric($payment['amount'] ?? null)
                    ? (float) $payment['amount']
                    : 0;
            });
    }

    public function getTotalReceivedProperty()
    {
        return collect($this->payments)
            ->sum(function ($payment) {
                return is_numeric($payment['amount'] ?? null)
                    ? (float) $payment['amount']
                    : 0;
            });
    }

    public function getRemainingProperty()
    {
        return max(
            ($this->order?->total ?? 0) - $this->totalReceived,
            0
        );
    }

    public function getChangeProperty()
    {
        return max(
            $this->totalReceived - ($this->order?->total ?? 0),
            0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DIVIDIR CUENTA
    |--------------------------------------------------------------------------
    */

    public function addPayment()
    {
        $this->payments[] = [
            'payment_method' => 'Efectivo',
            'amount' => 0,
        ];
    }

    public function removePayment($index)
    {
        unset($this->payments[$index]);

        $this->payments = array_values(
            $this->payments
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL PAGADO
    |--------------------------------------------------------------------------
    */

    public function getPaidTotalProperty()
    {
        return collect($this->payments)
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | COBRAR
    |--------------------------------------------------------------------------
    */

    public function pay()
    {
        if ($this->isProcessingPayment) {
            return;
        }
    
        $this->isProcessingPayment = true;
    
        try {
            
            $this->validate();
    
            $payments = collect($this->payments);
    
            /*
            |--------------------------------------------------------------------------
            | VALIDAR SOLO UN PAGO EN EFECTIVO
            |--------------------------------------------------------------------------
            */
    
            if (
                $payments
                    ->where('payment_method', 'Efectivo')
                    ->count() > 1
            ) {
                $this->addError(
                    'payments',
                    'Solo puede existir un pago en efectivo.'
                );
    
                return;
            }
    
            /*
            |--------------------------------------------------------------------------
            | VALIDAR SALDO PENDIENTE
            |--------------------------------------------------------------------------
            */
    
            if ($this->remaining > 0) {
    
                $this->addError(
                    'payments',
                    'Aún faltan $' . number_format($this->remaining, 0, ',', '.')
                    . ' para completar el pago.'
                );
    
                return;
            }
    
            /*
            |--------------------------------------------------------------------------
            | CALCULAR EFECTIVO NECESARIO
            |--------------------------------------------------------------------------
            */
    
            $electronicTotal = $payments
    
                ->whereIn('payment_method', [
                    'Nequi',
                    'Daviplata',
                    'QR',
                ])
    
                ->sum('amount');
    
            $cashRequired = max(
                $this->order->total - $electronicTotal,
                0
            );
    
            /*
            |--------------------------------------------------------------------------
            | VALIDAR EFECTIVO
            |--------------------------------------------------------------------------
            */
    
            $cashPayment = $payments
                ->firstWhere('payment_method', 'Efectivo');
    
            if ($cashPayment) {
    
                if ($cashPayment['amount'] < $cashRequired) {
    
                    $this->addError(
                        'payments',
                        'El cliente entregó $'
                        . number_format($cashPayment['amount'],0,',','.')
                        . ' en efectivo y se requieren al menos $'
                        . number_format($cashRequired,0,',','.')
                    );
    
                    return;
                }
    
                /*
                |--------------------------------------------------------------------------
                | SOLO SE REGISTRA EL VALOR APLICADO AL PEDIDO
                |--------------------------------------------------------------------------
                */
    
                $payments = $payments->map(function ($payment) use ($cashRequired) {
    
                    if ($payment['payment_method'] === 'Efectivo') {
                        $payment['amount'] = $cashRequired;
                    }
    
                    return $payment;
                });
            }
    
            /*
            |--------------------------------------------------------------------------
            | VALIDAR CAJA ABIERTA
            |--------------------------------------------------------------------------
            */
    
            $session = CashSession::query()
    
                ->open()
    
                ->where('user_id', auth()->id())
    
                ->where('id', $this->orderSessionId)
    
                ->first();
    
            if (!$session) {
    
                $this->dispatch(
                    'swal',
                    icon: 'error',
                    title: 'La caja de esta cuenta ya no está abierta. Verifica tus cajas activas.'
                );
    
                return;
            }
    
            /*
            |--------------------------------------------------------------------------
            | GUARDAR
            |--------------------------------------------------------------------------
            */
    
            DB::transaction(function () use ($session, $payments) {
    
                foreach ($payments as $payment) {
    
                    CashPayment::create([
    
                        'order_id' => $this->order->id,
    
                        'cash_session_id' => $session->id,
    
                        'cash_register_id' => $session->cash_register_id,
    
                        'user_id' => auth()->id(),
    
                        'payment_method' => $payment['payment_method'],
    
                        'amount' => $payment['amount'],
                    ]);
                }
    
                $this->order->update([
    
                    'cash_register_id' => $session->cash_register_id,
    
                    'cash_session_id' => $session->id,
    
                    'status_id' => 5,
    
                    'is_paid' => true,
    
                    'paid_at' => now(),
    
                    'closed_at' => now(),
    
                    'invoice_requested' => $this->invoice_requested,
                ]);
    
                if ($this->invoice_requested) {
                
                    $this->validate([
                
                        'invoice.document_type' => 'required',
                
                        'invoice.document' => 'required',
                
                        'invoice.name' => 'required',
                
                        'invoice.phone' => 'required',
                
                        'invoice.email' => 'nullable|email',
                
                        'invoice.address' => 'nullable',
                    ]);
                
                    $this->order->invoice()->create([
                
                        'document_type' => $this->invoice['document_type'],
                
                        'document'      => $this->invoice['document'],
                
                        'name'          => $this->invoice['name'],
                
                        'phone'         => $this->invoice['phone'],
                
                        'email'         => $this->invoice['email'],
                
                        'address'       => $this->invoice['address'],
                    ]);
                }
    
                if ($this->order->table_id) {
    
                    $this->order->diningTable?->releaseWithMerges();
                }
            });
    
            $this->dispatch('close-payment-modal');
    
            $this->dispatch(
                'swal',
                icon: 'success',
                title: 'Pago registrado correctamente'
            );
            
        } finally {
            $this->isProcessingPayment = false;
        }
    }
    
    public function confirmCancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
     
        $this->dispatch(
            'confirm-cancel-order',
            orderId: $order->id,
        );
    }
    
    #[On('cancelOrder')]
    public function cancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
     
        if ($order->status_id == 6) {
     
            $this->dispatch(
                'swal',
                icon: 'warning',
                title: 'El pedido ya se encuentra cancelado.'
            );
     
            return;
        }
     
     
        if ($order->is_paid) {
     
            $this->dispatch(
                'swal',
                icon: 'error',
                title: 'El pedido ya fue pagado.'
            );
     
            return;
        }
     
     
        DB::transaction(function () use ($order) {

            OrderCancellation::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'reason' => 'Cancelacion desde caja',
            ]);


            $order->update([
                'status_id' => 6,
                'closed_at' => now(),
            ]);

            (new SalesInventorySync())->reverseAllForOrder($order->load('details'));
     
     
            /*
            |--------------------------------------------------------------------------
            | LIBERAR MESA
            |--------------------------------------------------------------------------
            */
            if ($order->table_id) {
                $order->diningTable?->releaseWithMerges();
            }
        });
     
     
        $this->dispatch(
            'swal',
            icon: 'success',
            title: 'Pedido cancelado y mesa liberada.'
        );
     
     
        $this->dispatch('$refresh');
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $sessions = $this->openSessions();

        // Si no hay sesión activa seleccionada, o la que estaba seleccionada
        // ya se cerró (ej. el usuario la cerró desde otra pestaña/dispositivo),
        // se cae de vuelta a la primera sesión abierta disponible.
        if (
            !$this->activeSessionId
            || !$sessions->contains('id', $this->activeSessionId)
        ) {
            $this->activeSessionId = $sessions->first()?->id;
        }

        $activeSession = $sessions->firstWhere('id', $this->activeSessionId);

        $orders = collect();

        if ($activeSession) {

            $orders = Order::query()

                ->with([
                    'diningTable',
                    'floor',
                    'user',
                    'type',
                ])

                ->pendingPayment()

                ->whereNotIn('status_id', [
                    Order::STATUS_CANCELLED,
                    Order::STATUS_MERGED,
                ])

                ->where(
                    'floor_id',
                    $activeSession->cashRegister->floor_id
                )

                ->whereHas('type', function ($q) {
                    $q->where('name', 'Mesa');
                })

                ->orderBy('created_at')

                ->get();
        }

        return view(
            'livewire.cash.cashier-orders',
            compact('orders', 'sessions', 'activeSession')
        );
    }
}