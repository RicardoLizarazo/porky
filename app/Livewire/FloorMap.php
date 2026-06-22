<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Floor;
use App\Models\DiningTable;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class FloorMap extends Component
{
    public $selectedFloor;

    public function mount()
    {
        $this->selectedFloor = Floor::active()
            ->orderBy('sort_order')
            ->value('id');
    }

    public function openTable($tableId)
    {
        $table = DiningTable::with('activeOrder')
            ->findOrFail($tableId);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR ACCESO
        |--------------------------------------------------------------------------
        */

        if (!$table->canBeAccessedBy(Auth::user())) {

            $this->dispatch(
                'show-error',
                message: 'La mesa está siendo atendida por '.$table->waiter?->name
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR ORDEN SI NO EXISTE
        |--------------------------------------------------------------------------
        */

        if (!$table->activeOrder) {

            $order = Order::create([

                'customer_id' => null,

                'user_id' => Auth::id(),

                'location_id' => $table->location_id,
                'floor_id' => $table->floor_id,
                'table_id' => $table->id,

                'status_id' => 1,

                /*
                |--------------------------------------------------------------------------
                | TIPO RESTAURANTE
                |--------------------------------------------------------------------------
                */

                'type_id' => 3, // mesa/restaurante
            ]);

            $table->update([
                'waiter_id' => Auth::id(),
                'status' => DiningTable::OCCUPIED,
            ]);

        } else {

            $order = $table->activeOrder;
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIONAR POS
        |--------------------------------------------------------------------------
        */

        return redirect()->route('pos.show', $order->id);
    }

    public function render()
    {
        return view('restaurante.floor-map.index', [

            'floors' => Floor::active()
                ->orderBy('sort_order')
                ->get(),

            'tables' => DiningTable::with([
                    'floor',
                    'activeOrder.customer',
                    'waiter'
                ])
                ->where('floor_id', $this->selectedFloor)
                ->where('is_active', true)
                ->get(),
        ]);
    }
}