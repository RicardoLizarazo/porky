<?php

namespace App\Livewire\RestaurantReports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Location;
use App\Models\Floor;
use App\Models\Order;
use Carbon\Carbon;

class RestaurantReportsDashboard extends Component
{

    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    public $location_id;

    public $floor_id;

    public $date_from;
public $date_to;


    /*
    |--------------------------------------------------------------------------
    | DATOS
    |--------------------------------------------------------------------------
    */

    public $locations = [];

    public $floors = [];

    public $ordersCount = 0;

    public $totalSales = 0;

    public $totalOrders = 0;

    public $averageTicket = 0;


    public $salesByFloor = [];

    public $salesByTable = [];

    public $salesByWaiter = [];

    public $salesByPayment = [];

    public $topProducts = [];


    /*
    |--------------------------------------------------------------------------
    | INICIALIZAR
    |--------------------------------------------------------------------------
    */

public function mount()
{
    $this->locations = Location::all();

    // Rango por defecto: mes actual
    $this->date_from = now()->startOfMonth()->format('Y-m-d');
    $this->date_to   = now()->endOfMonth()->format('Y-m-d');

    $this->loadReports();
}

public function updatedDateFrom()
{
    $this->loadReports();
}

public function updatedDateTo()
{
    $this->loadReports();
}

private function getDateRange()
{
    return [
        Carbon::parse($this->date_from)->startOfDay(),
        Carbon::parse($this->date_to)->endOfDay(),
    ];
}



    /*
    |--------------------------------------------------------------------------
    | EVENTOS FILTROS
    |--------------------------------------------------------------------------
    */


    public function updatedLocationId()
    {
        $this->floor_id = null;

        $this->floors = Floor::query()
            ->where('location_id', $this->location_id)
            ->orderBy('name')
            ->get();

        $this->loadReports();
    }


    public function updatedFloorId()
    {
        $this->loadReports();
    }


    public function updatedRange()
    {
        $this->loadReports();
    }



    /*
    |--------------------------------------------------------------------------
    | FECHAS
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | CARGAR REPORTES
    |--------------------------------------------------------------------------
    */

    public function loadReports()
{

    $query = Order::query()
        ->where('is_paid', 1);


    /*
    |--------------------------------------------------------------------------
    | FILTRO SEDE
    |--------------------------------------------------------------------------
    */

    if($this->location_id){

        $query->where(
            'location_id',
            $this->location_id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FILTRO PISO
    |--------------------------------------------------------------------------
    */

    if($this->floor_id){

        $query->where(
            'floor_id',
            $this->floor_id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | RANGO FECHAS
    |--------------------------------------------------------------------------
    */
    [$from, $to] = $this->getDateRange();

    $query->whereBetween('created_at', [$from, $to]);


    /*
    |--------------------------------------------------------------------------
    | INDICADORES
    |--------------------------------------------------------------------------
    */


    $this->ordersCount = (clone $query)
        ->count();


    $this->totalSales = (clone $query)
        ->sum('total');


    $this->averageTicket =
        $this->ordersCount > 0
            ? $this->totalSales / $this->ordersCount
            : 0;



/*
|--------------------------------------------------------------------------
| VENTAS POR PISO
|--------------------------------------------------------------------------
*/
$this->salesByFloor = DB::table('orders')
    ->join('floors', 'orders.floor_id', '=', 'floors.id')
    ->select(
        'floors.id as floor_id',
        'floors.name as floor_name',
        DB::raw('SUM(orders.total) as total')
    )
    ->where('orders.is_paid', 1)
    ->when($this->location_id, function ($q) {
        $q->where('orders.location_id', $this->location_id);
    })
    ->when($this->floor_id, function ($q) {
        $q->where('orders.floor_id', $this->floor_id);
    })
    ->whereBetween('orders.created_at', $this->getDateRange())
    ->groupBy('floors.id', 'floors.name')
    ->get();


/*
|--------------------------------------------------------------------------
| VENTAS POR MESERO
|--------------------------------------------------------------------------
*/
$this->salesByWaiter = DB::table('orders')
    ->join('users', 'orders.user_id', '=', 'users.id')
    ->select(
        'users.id as user_id',
        'users.name as user_name',
        DB::raw('SUM(orders.total) as total')
    )
    ->where('orders.is_paid', 1)
    ->when($this->location_id, function ($q) {
        $q->where('orders.location_id', $this->location_id);
    })
    ->when($this->floor_id, function ($q) {
        $q->where('orders.floor_id', $this->floor_id);
    })
    ->whereBetween('orders.created_at', $this->getDateRange())
    ->groupBy('users.id', 'users.name')
    ->get();
}    

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.restaurant-reports.restaurant-reports-dashboard'
        );
    }

}