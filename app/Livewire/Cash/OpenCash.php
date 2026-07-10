<?php

namespace App\Livewire\Cash;

use Livewire\Component;
use App\Models\CashRegister;
use App\Models\CashSession;
use Livewire\Attributes\Validate;

class OpenCash extends Component
{
    #[Validate('required|exists:cash_registers,id')]
    public $cash_register_id;

    #[Validate('required|numeric|min:0')]
    public $opening_amount = 0;

    #[Validate('nullable|max:500')]
    public $opening_notes;

    public function open()
    {
        $this->validate();

        /*
        |--------------------------------------------------------------------------
        | VALIDAR USUARIO
        |--------------------------------------------------------------------------
        */

        $userHasSession = CashSession::open()
            ->where('user_id', auth()->id())
            ->exists();

        if ($userHasSession) {

            $this->dispatch(
                'swal',
                icon: 'warning',
                title: 'Ya tiene una caja abierta'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR CAJA
        |--------------------------------------------------------------------------
        */

        $cashOpen = CashSession::open()
            ->where(
                'cash_register_id',
                $this->cash_register_id
            )
            ->exists();

        if ($cashOpen) {

            $this->dispatch(
                'swal',
                icon: 'warning',
                title: 'La caja ya está abierta'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR SESIÓN
        |--------------------------------------------------------------------------
        */

        CashSession::create([

            'cash_register_id' => $this->cash_register_id,

            'user_id' => auth()->id(),

            'opened_by' => auth()->id(),

            'opening_amount' => $this->opening_amount,

            'opening_notes' => $this->opening_notes,

            'opened_at' => now(),

            'is_open' => true,
        ]);

        $this->dispatch(
            'swal',
            icon: 'success',
            title: 'Caja abierta correctamente'
        );

        return redirect()
             ->route('cashier.dashboard');
    }

    public function render()
    {
        return view(
            'livewire.cash.open-cash',
            [
            'cashRegisters' => CashRegister::query()

                ->active()

                ->where('responsible_user_id', auth()->id())

                ->whereDoesntHave(
                    'sessions',
                    fn ($q) => $q->where('is_open', true)
                )

                ->orderBy('name')

                ->get()
            ]
        );
    }
}