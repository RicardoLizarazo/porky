<?php

namespace App\Livewire\Cashier;

use Livewire\Component;

class OpenCashier extends Component
{
    public CashRegister $cashRegister;

    public $opening_amount = 0;

    public $observations = '';

    public function render()
    {
        return view('livewire.cashier.open-cashier');
    }
}
