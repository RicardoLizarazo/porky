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

        // 2. Base: pedidos por mesero
        $waiters = DB::table('orders')
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

        // 3. Desglose por método de pago, agrupado por mesero
        $paymentsByWaiter = DB::table('cash_payments')
            ->join('orders', 'cash_payments.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'cash_payments.payment_method',
                DB::raw('SUM(cash_payments.amount) as amount')
            )
            ->where('orders.is_paid', 1)
            ->when($this->location_id, fn($q) => $q->where('orders.location_id', $this->location_id))
            ->when($this->floor_id, fn($q) => $q->where('orders.floor_id', $this->floor_id))
            ->whereBetween('orders.created_at', $this->getDateRange())
            ->groupBy('users.id', 'cash_payments.payment_method')
            ->get()
            ->groupBy('user_id');

        // 4. Combinar
        $this->salesByWaiter = $waiters->map(function ($waiter) use ($paymentsByWaiter) {

            $methodTotals = [];
            foreach ($this->paymentMethods as $method) {
                $methodTotals[$method] = 0;
            }

            if (isset($paymentsByWaiter[$waiter->user_id])) {
                foreach ($paymentsByWaiter[$waiter->user_id] as $payment) {
                    $methodTotals[$payment->payment_method] =
                        ($methodTotals[$payment->payment_method] ?? 0) + $payment->amount;
                }
            }

            $waiter->payments = $methodTotals;

            return $waiter;
        });
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