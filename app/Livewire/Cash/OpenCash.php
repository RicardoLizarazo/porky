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
        | VALIDAR CAJA
        |--------------------------------------------------------------------------
        |
        | Nota: ya NO se valida "el usuario no puede tener más de una caja
        | abierta". Un mismo usuario puede ser responsable de varias cajas
        | físicas (ej. Salón Principal, Salón Rojo, Salón Blanco) y debe poder
        | tenerlas todas abiertas a la vez, sin cerrar una para abrir otra.
        | Lo único que se sigue validando es que ESA caja puntual no esté ya
        | abierta (por este u otro usuario), y el listado del formulario ya
        | solo muestra cajas asignadas a él (responsible_user_id) que no
        | tienen sesión abierta.
        |
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
                ->get(),

            // Cajas que este usuario ya tiene abiertas ahora mismo, para
            // mostrarle contexto en pantalla (ej. "ya tienes abiertas: Salón
            // Principal, Salón Rojo").
            'openSessions' => CashSession::open()
                ->with('cashRegister')
                ->where('user_id', auth()->id())
                ->get(),
            ]
        );
    }
}
