<?php
// app/Livewire/RestaurantReports/FloorReport.php

namespace App\Livewire\RestaurantReports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Livewire\RestaurantReports\Concerns\HasReportFilters;
use App\Exports\FloorSalesExport;
use Maatwebsite\Excel\Facades\Excel;

class FloorReport extends Component
{
    use HasReportFilters;

    public $salesByFloor = [];

    public function mount()
    {
        $this->mountFilters();
        $this->loadReport();
    }

    public function loadReport()
    {
        // Metodos de pago existentes en el rango filtrado (dinamico)
        $availableMethods = DB::table('cash_payments')
            ->join('orders', 'cash_payments.order_id', '=', 'orders.id')
            ->where('orders.is_paid', 1)
            ->when($this->location_id, fn($q) => $q->where('orders.location_id', $this->location_id))
            ->when($this->floor_id, fn($q) => $q->where('orders.floor_id', $this->floor_id))
            ->whereBetween('orders.created_at', $this->getDateRange())
            ->distinct()
            ->pluck('cash_payments.payment_method')
            ->filter()
            ->sort()
            ->values();
    
        $this->paymentMethods = $availableMethods->mapWithKeys(fn($m) => [$m => $m])->toArray();
    
        // 1. Base: pedidos por piso
        $floors = DB::table('orders')
            ->leftJoin('floors', 'orders.floor_id', '=', 'floors.id')
            ->select(
                'floors.id as floor_id',
                'floors.name as floor_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.total) as total')
            )
            ->where('orders.is_paid', 1)
            ->when($this->location_id, fn($q) => $q->where('orders.location_id', $this->location_id))
            ->when($this->floor_id, fn($q) => $q->where('orders.floor_id', $this->floor_id))
            ->whereBetween('orders.created_at', $this->getDateRange())
            ->groupBy('floors.id', 'floors.name')
            ->orderByDesc('total')
            ->get();
    
        // 2. Desglose por metodo de pago, agrupado por piso
        $paymentsByFloor = DB::table('cash_payments')
            ->join('orders', 'cash_payments.order_id', '=', 'orders.id')
            ->leftJoin('floors', 'orders.floor_id', '=', 'floors.id')
            ->select(
                'floors.id as floor_id',
                'cash_payments.payment_method',
                DB::raw('SUM(cash_payments.amount) as amount')
            )
            ->where('orders.is_paid', 1)
            ->when($this->location_id, fn($q) => $q->where('orders.location_id', $this->location_id))
            ->when($this->floor_id, fn($q) => $q->where('orders.floor_id', $this->floor_id))
            ->whereBetween('orders.created_at', $this->getDateRange())
            ->groupBy('floors.id', 'cash_payments.payment_method')
            ->get()
            ->groupBy('floor_id');
    
        // 3. Combinar
        $this->salesByFloor = $floors->map(function ($floor) use ($paymentsByFloor) {
    
            $methodTotals = [];
            foreach ($this->paymentMethods as $key => $label) {
                $methodTotals[$key] = 0;
            }
    
            if (isset($paymentsByFloor[$floor->floor_id])) {
                foreach ($paymentsByFloor[$floor->floor_id] as $payment) {
                    $methodTotals[$payment->payment_method] =
                        ($methodTotals[$payment->payment_method] ?? 0) + $payment->amount;
                }
            }
    
            $floor->payments = $methodTotals;
    
            return $floor;
        });
    }

    public function exportExcel()
    {
        return Excel::download(
            new FloorSalesExport(
                $this->location_id,
                $this->floor_id,
                $this->date_from,
                $this->date_to
            ),
            'ventas-por-piso-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.restaurant-reports.floor-report', [
            'paymentMethods' => $this->paymentMethods,
        ]);
    }
}