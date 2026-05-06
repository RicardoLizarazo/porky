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

    // ================= RANGO =================

    private function getDateRange()
    {
        return match ($this->range) {
            'today' => [Carbon::today(), Carbon::today()],
            'week'  => [now()->subDays(7), now()],
            'month' => [now()->startOfMonth(), now()],
            'all'   => [null, null],
        };
    }

    // ================= HELPERS DE FECHA =================

    // 🔹 SIN JOIN
    private function applyDateFilterSimple($query, $from, $to)
    {
        $column = DB::raw("COALESCE(ordered_at, created_at)");

        if (!$from && !$to) return $query;

        if ($from && !$to) return $query->where($column, '>=', $from);

        if (!$from && $to) return $query->where($column, '<=', $to);

        return $query->whereBetween($column, [$from, $to]);
    }

    // 🔹 CON JOIN (alias obligatorio)
    private function applyDateFilterWithAlias($query, $from, $to, $alias = 'o')
    {
        $column = DB::raw("COALESCE($alias.ordered_at, $alias.created_at)");

        if (!$from && !$to) return $query;

        if ($from && !$to) return $query->where($column, '>=', $from);

        if (!$from && $to) return $query->where($column, '<=', $to);

        return $query->whereBetween($column, [$from, $to]);
    }

    // ================= DATA =================

    public function loadData()
    {
        [$from, $to] = $this->getDateRange();

        $statusDelivered = 5;

        // ================= KPI =================
        $base = DB::table('orders')
            ->where('status_id', $statusDelivered)
            ->whereNotNull('delivery_user_id'); // evita datos legacy

        $this->applyDateFilterSimple($base, $from, $to);

        $totalSales  = (clone $base)->sum('total');
        $totalOrders = (clone $base)->count();

        // ================= PERIODO ANTERIOR =================
        if ($from && $to) {

            $days = $to->diffInDays($from);

            $prevFrom = (clone $from)->subDays($days);
            $prevTo   = (clone $from);

            $prev = DB::table('orders')
                ->where('status_id', $statusDelivered)
                ->whereNotNull('delivery_user_id');

            $this->applyDateFilterSimple($prev, $prevFrom, $prevTo);

            $prevSales = $prev->sum('total');

        } else {
            $prevSales = 0;
        }

        $growth = $prevSales > 0
            ? (($totalSales - $prevSales) / $prevSales) * 100
            : 0;

        $this->kpis = [
            'sales'  => $totalSales,
            'orders' => $totalOrders,
            'avg'    => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'growth' => $growth,
        ];

        // ================= DAILY =================
        $daily = DB::table('orders')
            ->selectRaw("DATE(COALESCE(ordered_at, created_at)) as date, SUM(total) as total")
            ->where('status_id', $statusDelivered)
            ->whereNotNull('delivery_user_id');

        $this->applyDateFilterSimple($daily, $from, $to);

        $this->dailySales = $daily
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ================= MONTHLY =================
        $monthly = DB::table('orders')
            ->selectRaw("DATE_FORMAT(COALESCE(ordered_at, created_at), '%Y-%m') as mes, SUM(total) as total")
            ->where('status_id', $statusDelivered)
            ->whereNotNull('delivery_user_id');

        $this->applyDateFilterSimple($monthly, $from, $to);

        $this->monthlySales = $monthly
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // ================= TOP PRODUCTOS =================
        $top = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->select('p.name', DB::raw('SUM(od.quantity) as total'))
            ->where('o.status_id', $statusDelivered)
            ->whereNotNull('o.delivery_user_id');

        $this->applyDateFilterWithAlias($top, $from, $to, 'o');

        $this->topProducts = $top
            ->groupBy('p.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ================= LOW PRODUCTOS =================
        $low = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->select('p.name', DB::raw('SUM(od.quantity) as total'))
            ->where('o.status_id', $statusDelivered)
            ->whereNotNull('o.delivery_user_id');

        $this->applyDateFilterWithAlias($low, $from, $to, 'o');

        $this->lowProducts = $low
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