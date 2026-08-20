<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Floor;
use App\Models\DiningTable;

class VitrinaAccess extends Component
{
    public $pin = '';

    public $error = false;

    /*
    |--------------------------------------------------------------------------
    | TECLADO
    |--------------------------------------------------------------------------
    */

    public function pressDigit($digit)
    {
        if (strlen($this->pin) >= 4) {
            return;
        }

        $this->error = false;
        $this->pin .= $digit;

        if (strlen($this->pin) === 4) {
            $this->attemptUnlock();
        }
    }

    public function clearPin()
    {
        $this->pin = '';
        $this->error = false;
    }

    public function backspace()
    {
        $this->pin = substr($this->pin, 0, -1);
        $this->error = false;
    }

    /*
    |--------------------------------------------------------------------------
    | DESBLOQUEO
    |--------------------------------------------------------------------------
    */

    public function attemptUnlock()
    {
        if (!hash_equals((string) config('pos.vitrina_pin'), (string) $this->pin)) {
            $this->error = true;
            $this->pin = '';
            return;
        }

        $table = $this->resolveVitrinaTable();

        if (!$table) {
            $this->error = true;
            $this->pin = '';
            $this->dispatch(
                'swal',
                icon: 'error',
                title: 'No se encontró la mesa de Vitrina. Contacta al administrador.'
            );
            return;
        }

        // TODO: reemplazar por la MISMA lógica que usa el mapa de mesas
        // en openTable($id) para abrir/crear la orden — pendiente de
        // confirmar el método exacto para no duplicar campos distintos.
        $order = $table->activeOrder()->first();

        if (!$order) {
            $order = \App\Models\Order::create([
                'table_id'    => $table->id,
                'floor_id'    => $table->floor_id,
                'user_id'     => auth()->id(),
                'status_id'   => 1, // abierta
            ]);
        }

        return redirect()->route('pos.order', $order);
    }

    protected function resolveVitrinaTable()
    {
        $floor = Floor::where('name', 'Vitrina')->first();

        if (!$floor) {
            return null;
        }

        return DiningTable::where('floor_id', $floor->id)
            ->where('is_active', true)
            ->first();
    }

    public function render()
    {
        return view('restaurante.pos.vitrina-access');
    }
}