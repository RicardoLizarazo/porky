<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\DiningTable;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class VitrinaQuickOpenController extends Controller
{
    /**
     * Atajo directo a la mesa "Vitrina {station}", sin pasar por el
     * mapa de pisos/mesas. Usa la MISMA lógica que FloorMap::openTable(),
     * con la sesión normal de Laravel (el picador se loguea como
     * cualquier mesero, no hay PIN ni cuenta compartida).
     */
    public function open($station)
    {
        $floor = Floor::where('name', 'Vitrina')->first();

        abort_if(!$floor, 404, 'No existe el piso "Vitrina". Contacta al administrador.');

        $table = DiningTable::with('activeOrder')
            ->where('floor_id', $floor->id)
            ->where('name', 'Vitrina ' . $station)
            ->where('is_active', true)
            ->first();

        abort_if(!$table, 404, 'No existe la mesa "Vitrina ' . $station . '". Contacta al administrador.');

        $user = Auth::user();
        $canOpenTables = $user->can('floor_map.open_table');
        $canQuickAdd   = $user->can('floor_map.add_product');

        /*
        |----------------------------------------------------------------
        | CASO 1: la estación ya tiene una orden activa (sin pagar todavía)
        |----------------------------------------------------------------
        */
        if ($table->activeOrder) {
            if ($canQuickAdd || ($canOpenTables && $table->canBeAccessedBy($user))) {
                return redirect()->route('pos.show', $table->activeOrder->id);
            }

            abort(403, 'Esta estación está siendo atendida por ' . $table->waiter?->name);
        }

        /*
        |----------------------------------------------------------------
        | CASO 2: estación libre -> se crea una orden nueva
        |----------------------------------------------------------------
        */
        if (!$canOpenTables) {
            abort(403, 'Tu cuenta no tiene permiso para abrir esta estación.');
        }

        $order = Order::create([
            'customer_id' => null,
            'user_id'     => Auth::id(),
            'location_id' => $table->location_id,
            'floor_id'    => $table->floor_id,
            'table_id'    => $table->id,
            'status_id'   => 1,
            'type_id'     => 3, // mesa/restaurante
        ]);

        $table->update([
            'waiter_id' => Auth::id(),
            'status'    => DiningTable::OCCUPIED,
        ]);

        return redirect()->route('pos.show', $order->id);
    }
}