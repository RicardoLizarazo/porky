<?php

namespace App\Http\Controllers;

use App\Models\CashSession;

class CashCloseController extends Controller
{
    public function print(CashSession $cashSession)
    {
        $cashSession->load([

            'cashRegister.location',

            'cashRegister.floor',

            'user',

            'payments',

            'orders',

        ]);

        return view(

            'livewire.cash.print-close',

            compact('cashSession')

        );
    }
}