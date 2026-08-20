<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CashSession;

class CashierDashboard extends Component
{
    public $sessions;

    public function mount()
    {
        $this->loadSessions();
    }

    public function loadSessions()
    {
        $this->sessions = CashSession::query()

            ->with([
                'cashRegister.location',
                'cashRegister.floor',
                'user',
            ])

            ->where('user_id', auth()->id())

            ->open()

            ->latest()

            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | TOTALES CONSOLIDADOS (suma de todas las cajas abiertas del usuario)
    |--------------------------------------------------------------------------
    */

    public function getTotalSalesProperty()
    {
        return $this->sessions->sum->sales_total;
    }

    public function getTotalCashProperty()
    {
        return $this->sessions->sum->cash_total;
    }

    public function getTotalDigitalProperty()
    {
        return $this->sessions->sum(function ($session) {
            return $session->nequi_total
                + $session->daviplata_total
                + $session->qr_total;
        });
    }

    public function getTotalExpectedCashProperty()
    {
        return $this->sessions->sum->expected_cash;
    }

    public function getTotalOrdersProperty()
    {
        return $this->sessions->sum->orders_count;
    }

    public function render()
    {
        return view(
            'restaurante.cashier.dashboard'
        );
    }
}