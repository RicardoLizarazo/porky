<?php

namespace App\Livewire\Cash;

use Livewire\Component;
use App\Models\CashSession;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CloseCash extends Component
{
    public $session_id;

    public $session;

    public $counted_cash = 0;

    public $closing_notes = '';

    protected $rules = [

        'counted_cash'  => 'required|numeric|min:0',

        'closing_notes' => 'nullable|string|max:1000',

    ];
    
    public $lastClosedSessionId = null;

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount()
    {
        $openSessions = $this->userOpenSessions();

        // Si solo tiene una caja abierta, se entra directo al cierre (igual
        // que antes). Si tiene varias (ej. Salon Principal, Salon Rojo,
        // Salon Blanco), primero debe elegir cual va a cerrar.
        if ($openSessions->count() === 1) {

            $this->selectSession(
                $openSessions->first()->id
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SELECCION DE CAJA A CERRAR
    |--------------------------------------------------------------------------
    */

    protected function userOpenSessions()
    {
        return CashSession::query()

            ->with([
                'cashRegister.location',
                'cashRegister.floor',
                'user',
            ])

            ->open()

            ->where('user_id', auth()->id())

            ->get();
    }

    public function selectSession($sessionId)
    {
        $this->session = $this->userOpenSessions()
            ->firstWhere('id', $sessionId);

        $this->session_id = $this->session?->id;

        $this->counted_cash = $this->session?->expected_cash ?? 0;

        $this->closing_notes = '';
    }

    public function changeSession()
    {
        $this->reset([
            'session',
            'session_id',
            'counted_cash',
            'closing_notes',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CERRAR CAJA
    |--------------------------------------------------------------------------
    */

    public function closeCash()
    {
        $this->validate();

        if (!$this->session) {

            $this->dispatch(
                'swal',
                icon: 'error',
                title: 'No existe una caja abierta.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR PEDIDOS PENDIENTES DE COBRO
        |--------------------------------------------------------------------------
        */

        $pendingOrders = Order::query()

            ->where('cash_session_id', $this->session->id)

            ->where('is_paid', false)

            ->count();

        if ($pendingOrders > 0) {

            $this->dispatch(
                'swal',
                icon: 'error',
                title: "Existen {$pendingOrders} pedidos pendientes de cobro."
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR MESAS OCUPADAS
        |--------------------------------------------------------------------------
        */

        $occupiedTables = Order::query()

            ->where('cash_session_id', $this->session->id)

            ->whereNotNull('table_id')

            ->where('status_id', '<>', 5)

            ->count();

        if ($occupiedTables > 0) {

            $this->dispatch(
                'swal',
                icon: 'error',
                title: "Existen {$occupiedTables} mesas que aún no han sido cerradas."
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CALCULAR DIFERENCIA
        |--------------------------------------------------------------------------
        */

        $difference = $this->counted_cash
            - $this->session->expected_cash;

        /*
        |--------------------------------------------------------------------------
        | CERRAR SESION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($difference) {

            $notes = trim($this->closing_notes);

            if ($difference > 0) {

                $notes .= PHP_EOL .
                    "Sobrante: $" .
                    number_format($difference,0,',','.');

            }

            if ($difference < 0) {

                $notes .= PHP_EOL .
                    "Faltante: $" .
                    number_format(abs($difference),0,',','.');
            }

            $this->session->update([

                'closing_amount' => $this->counted_cash,

                'closing_notes'  => trim($notes),

                'closed_at'      => now(),

                'closed_by'      => auth()->id(),

                'is_open'        => false,

            ]);
        });
        
        $this->lastClosedSessionId = $this->session->id;
        
        $this->dispatch(
            'swal',
            icon: 'success',
            title: 'Caja cerrada correctamente.'
        );

        // Si el usuario tiene otras cajas abiertas (ej. le falta cerrar el
        // Salon Rojo y el Salon Blanco), lo devolvemos al selector en vez de
        // sacarlo del flujo de cierre.
        $this->dispatch('open-print', url: route('cash.close.print', $this->lastClosedSessionId));
        $remainingOpen = $this->userOpenSessions();

        $this->reset([
            'session',
            'session_id',
            'counted_cash',
            'closing_notes',
        ]);

        if ($remainingOpen->isEmpty()) {

            return redirect()->route('cash.open');
        }

        if ($remainingOpen->count() === 1) {

            $this->selectSession(
                $remainingOpen->first()->id
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getDifferenceProperty()
    {
        if (!$this->session) {
            return 0;
        }

        return $this->counted_cash
            - $this->session->expected_cash;
    }

    public function getDifferenceColorProperty()
    {
        if ($this->difference > 0) {
            return 'success';
        }

        if ($this->difference < 0) {
            return 'danger';
        }

        return 'secondary';
    }

    public function getDifferenceLabelProperty()
    {
        if ($this->difference > 0) {
            return 'Sobrante';
        }

        if ($this->difference < 0) {
            return 'Faltante';
        }

        return 'Sin diferencia';
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.cash.close-cash',
            [
                'openSessions' => $this->userOpenSessions(),
            ]
        );
    }
}