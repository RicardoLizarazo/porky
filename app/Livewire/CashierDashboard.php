<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CashSession;

class CashierDashboard extends Component
{
    public $session;

    public function mount()
    {
        $this->loadSession();
    }

    public function loadSession()
    {
        $this->session = CashSession::query()

            ->with([
                'cashRegister.location',
                'cashRegister.floor',
                'user',
            ])

            ->where('user_id', auth()->id())

            ->open()

            ->latest()

            ->first();
    }

    public function render()
    {
        return view(
            'restaurante.cashier.dashboard'
        );
    }
}