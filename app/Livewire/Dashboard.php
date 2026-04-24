<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $range = 'month';

    public $kpis = [];
    public $dailySales = [];
    public $monthlySales = [];
    public $topProducts = [];
    public $lowProducts = [];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedRange()
    {
        $this->loadData();
    }

    private function getDateRange()
    {
        return match ($this->range) {
            'today' => [Carbon::today(), Carbon::today()],
            'week' => [Carbon::now()->subDays(7), Carbon::now()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()],
        };
    }

    public function loadData()
    {
        [$from, $to] = $this->getDateRange();

        $statusDelivered = 5;

        // ================= KPIs =================
        $base = DB::table('orders')
            ->where('status_id', $statusDelivered)
            ->whereBetween('created_at', [$from, $to]);

        $totalSales = $base->sum('total');
        $totalOrders = $base->count();

        // período anterior
        $prevFrom = (clone $from)->subDays($to->diffInDays($from));
        $prevTo = $from;

        $prevSales = DB::table('orders')
            ->where('status_id', $statusDelivered)
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->sum('total');

        $growth = $prevSales > 0
            ? (($totalSales - $prevSales) / $prevSales) * 100
            : 0;

        $this->kpis = [
            'sales' => $totalSales,
            'orders' => $totalOrders,
            'avg' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'growth' => $growth,
        ];

        // ================= VENTAS DIARIAS =================
        $this->dailySales = DB::table('orders')
            ->selectRaw("DATE(created_at) as date, SUM(total) as total")
            ->where('status_id', $statusDelivered)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ================= VENTAS MENSUALES =================
        $this->monthlySales = DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, SUM(total) as total")
            ->where('status_id', $statusDelivered)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // ================= TOP PRODUCTOS =================
        $this->topProducts = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->select('p.name', DB::raw('SUM(od.quantity) as total'))
            ->where('o.status_id', $statusDelivered)
            ->whereBetween('o.created_at', [$from, $to])
            ->groupBy('p.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ================= PRODUCTOS LENTOS =================
        $this->lowProducts = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->select('p.name', DB::raw('SUM(od.quantity) as total'))
            ->where('o.status_id', $statusDelivered)
            ->whereBetween('o.created_at', [$from, $to])
            ->groupBy('p.name')
            ->orderBy('total')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('dashboard');
    }
}