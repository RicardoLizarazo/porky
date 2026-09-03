<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Floor;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VitrinaAccess extends Component
{
    public $station;

    public $pin = '';

    public $error = false;

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount($station)
    {
        $this->station = $station;

        // El PIN es un desbloqueo de SESIÓN (una vez por turno en este
        // dispositivo/estación), no algo que se repita en cada pedido.
        // Si ya se desbloqueó antes en esta sesión, se pasa directo a
        // abrir/crear el pedido, sin volver a pedir el PIN.
        if (session('vitrina_unlocked_' . $this->station)) {
            if (!$this->loginAsVitrina()) {
                return;
            }
            return $this->openOrCreateOrder();
        }
    }

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
    | DESBLOQUEO (solo la primera vez por turno en esta estación)
    |--------------------------------------------------------------------------
    */

    public function attemptUnlock()
    {
        if (!hash_equals((string) config('pos.vitrina_pin'), (string) $this->pin)) {
            $this->error = true;
            $this->pin = '';
            return;
        }

        session(['vitrina_unlocked_' . $this->station => true]);

        if (!$this->loginAsVitrina()) {
            return;
        }

        return $this->openOrCreateOrder();
    }

    /**
     * Inicia sesión con la cuenta compartida de Vitrina, sin pedirle
     * usuario/contraseña al picador — el PIN es la única autorización.
     * Siempre re-loguea explícitamente (no confía en auth()->check()),
     * para evitar estados de sesión a medias entre peticiones.
     */
    protected function loginAsVitrina()
    {
        $vitrinaUser = User::where('email', config('pos.vitrina_user_email'))->first();

        if (!$vitrinaUser) {
            $this->error = true;
            $this->dispatch(
                'swal',
                icon: 'error',
                title: 'No existe la cuenta de Vitrina configurada. Contacta al administrador.'
            );
            return false;
        }

        Auth::login($vitrinaUser);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | ABRIR / CREAR PEDIDO (misma lógica que openTable() del mapa de mesas)
    |--------------------------------------------------------------------------
    */
    protected function openOrCreateOrder()
    {
        $table = $this->resolveVitrinaTable();

        if (!$table) {
            $this->error = true;
            $this->dispatch(
                'swal',
                icon: 'error',
                title: "No se encontró la mesa \"Vitrina {$this->station}\". Contacta al administrador."
            );
            return;
        }

        $user = auth()->user();

        if (!$user) {
            $this->error = true;
            $this->dispatch(
                'swal',
                icon: 'error',
                title: 'No se pudo validar la sesión de Vitrina. Vuelve a marcar el PIN.'
            );
            session()->forget('vitrina_unlocked_' . $this->station);
            return;
        }

        $canOpenTables = $user->can('floor_map.open_table');
        $canQuickAdd   = $user->can('floor_map.add_product');

        $table->load('activeOrder');

        // CASO 1: esta estación quedó con una orden sin cerrar
        // (no se llegó a "Enviar a Cocina") — se retoma.
        if ($table->activeOrder) {
            if ($canQuickAdd || ($canOpenTables && $table->canBeAccessedBy($user))) {
                return redirect()->route('pos.show', $table->activeOrder->id);
            }

            $this->dispatch(
                'swal',
                icon: 'error',
                title: 'Esta estación está siendo atendida por '.$table->waiter?->name
            );
            return;
        }

        // CASO 2: estación libre -> se crea una orden nueva
        if (!$canOpenTables) {
            $this->dispatch(
                'swal',
                icon: 'error',
                title: 'Esta cuenta no tiene permiso para abrir esta estación.'
            );
            return;
        }

        $order = Order::create([
            'customer_id' => null,
            'user_id'     => auth()->id(),
            'location_id' => $table->location_id,
            'floor_id'    => $table->floor_id,
            'table_id'    => $table->id,
            'status_id'   => 1,
            'type_id'     => 3, // mesa/restaurante
        ]);

        $table->update([
            'waiter_id' => auth()->id(),
            'status'    => DiningTable::OCCUPIED,
        ]);

        return redirect()->route('pos.show', $order->id);
    }

    protected function resolveVitrinaTable()
    {
        $floor = Floor::where('name', 'Vitrina')->first();

        if (!$floor) {
            return null;
        }

        return DiningTable::where('floor_id', $floor->id)
            ->where('is_active', true)
            ->where('name', 'Vitrina ' . $this->station)
            ->first();
    }

    public function render()
    {
        return view('restaurante.pos.vitrina-keypad');
    }
}