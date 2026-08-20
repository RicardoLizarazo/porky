<?php
// app/Livewire/RestaurantReports/WaiterReport.php

namespace App\Livewire\RestaurantReports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Livewire\RestaurantReports\Concerns\HasReportFilters;
use App\Exports\WaiterSalesExport;
use Maatwebsite\Excel\Facades\Excel;

class WaiterReport extends Component
{
    use HasReportFilters;

    public $salesByWaiter = [];

    public function mount()
    {
        $this->mountFilters();
        $this->loadReport();
    }

    public function loadReport()
    {
        $this->salesByWaiter = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.total) as total')
            )
            ->where('orders.is_paid', 1)
            ->when($this->location_id, fn($q) => $q->where('orders.location_id', $this->location_id))
            ->when($this->floor_id, fn($q) => $q->where('orders.floor_id', $this->floor_id))
            ->whereBetween('orders.created_at', $this->getDateRange())
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->get();
    }

    public function exportExcel()
    {
        return Excel::download(
            new WaiterSalesExport(
                $this->location_id,
                $this->floor_id,
                $this->date_from,
                $this->date_to
            ),
            'ventas-por-mesero-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.restaurant-reports.waiter-report');
    }
}