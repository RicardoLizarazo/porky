<?php
// app/Livewire/RestaurantReports/TableReport.php

namespace App\Livewire\RestaurantReports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Livewire\RestaurantReports\Concerns\HasReportFilters;
use App\Exports\TableSalesExport;
use Maatwebsite\Excel\Facades\Excel;

class TableReport extends Component
{
    use HasReportFilters;

    public $salesByTable = [];

    public $paymentMethods = [];

    public function mount()
    {
        $this->mountFilters();
        $this->loadReport();
    }

    public function loadReport()
    {
        // 1. Métodos de pago existentes en el rango filtrado (dinámico)
        $this->paymentMethods = DB::table('cash_payments')
            ->join('orders', 'cash_payments.order_id', '=', 'orders.id')
            ->where('orders.is_paid', 1)
            ->when($this->location_id, fn($q) => $q->where('orders.location_id', $this->location_id))
            ->when($this->floor_id, fn($q) => $q->where('orders.floor_id', $this->floor_id))
            ->whereBetween('orders.created_at', $this->getDateRange())
            ->distinct()
            ->pluck('cash_payments.payment_method')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        // 2. Base: pedidos por mesa
        $tables = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->leftJoin('floors', 'orders.floor_id', '=', 'floors.id')
            ->select(
                'tables.id as table_id',
                'tables.name as table_name',
                'floors.name as floor_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.total) as total')
            )
            ->where('orders.is_paid', 1)
            ->when($this->location_id, fn($q) => $q->where('orders.location_id', $this->location_id))
            ->when($this->floor_id, fn($q) => $q->where('orders.floor_id', $this->floor_id))
            ->whereBetween('orders.created_at', $this->getDateRange())
            ->groupBy('tables.id', 'tables.name', 'floors.name')
            ->orderByDesc('total')
            ->get();

        // 3. Desglose por método de pago, agrupado por mesa
        $paymentsByTable = DB::table('cash_payments')
            ->join('orders', 'cash_payments.order_id', '=', 'orders.id')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->select(
                'tables.id as table_id',
                'cash_payments.payment_method',
                DB::raw('SUM(cash_payments.amount) as amount')
            )
            ->where('orders.is_paid', 1)
            ->when($this->location_id, fn($q) => $q->where('orders.location_id', $this->location_id))
            ->when($this->floor_id, fn($q) => $q->where('orders.floor_id', $this->floor_id))
            ->whereBetween('orders.created_at', $this->getDateRange())
            ->groupBy('tables.id', 'cash_payments.payment_method')
            ->get()
            ->groupBy('table_id');

        // 4. Combinar
        $this->salesByTable = $tables->map(function ($table) use ($paymentsByTable) {

            $methodTotals = [];
            foreach ($this->paymentMethods as $method) {
                $methodTotals[$method] = 0;
            }

            if (isset($paymentsByTable[$table->table_id])) {
                foreach ($paymentsByTable[$table->table_id] as $payment) {
                    $methodTotals[$payment->payment_method] =
                        ($methodTotals[$payment->payment_method] ?? 0) + $payment->amount;
                }
            }

            $table->payments = $methodTotals;

            return $table;
        });
    }

    public function exportExcel()
    {
        return Excel::download(
            new TableSalesExport(
                $this->location_id,
                $this->floor_id,
                $this->date_from,
                $this->date_to
            ),
            'ventas-por-mesa-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.restaurant-reports.table-report');
    }
}