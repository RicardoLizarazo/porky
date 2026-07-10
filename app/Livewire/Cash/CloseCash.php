<?php

namespace App\Livewire\Cash;

use Livewire\Component;
use App\Models\CashSession;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CloseCash extends Component
{
    public $session;

    public $counted_cash = 0;

    public $closing_notes = '';

    protected $rules = [

        'counted_cash'  => 'required|numeric|min:0',

        'closing_notes' => 'nullable|string|max:1000',

    ];

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount()
    {
        $this->session = CashSession::query()

            ->with([
                'cashRegister.location',
                'cashRegister.floor',
                'user',
            ])

            ->open()

            ->where('user_id', auth()->id())

            ->first();

        if ($this->session) {

            $this->counted_cash = $this->session->expected_cash;
        }
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
        | CERRAR SESIÓN
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

        $this->dispatch(
            'swal',
            icon: 'success',
            title: 'Caja cerrada correctamente.'
        );

        return redirect()->route('cash.open');
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
        return view('livewire.cash.close-cash');
    }
}