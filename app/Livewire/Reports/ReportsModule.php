<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsReportExport;
use App\Exports\CustomersReportExport;
use App\Exports\DeliveryReportExport;
use App\Exports\PaymentReportExport;

class ReportsModule extends Component
{
    public $from;
    public $to;

    public $report = 'products';

    public $data = [];

    const STATUS_DELIVERED = 5;

    public function mount($type = 'products')
    {
        $this->report = $type;

        // 🔥 SIN fechas por defecto = histórico completo
        $this->from = null;
        $this->to   = null;

        $this->generate();
    }

    public function updated($field)
    {
        if (in_array($field, ['from', 'to', 'report'])) {
            $this->generate();
        }
    }

    // ================= FILTRO GLOBAL DE FECHA =================

    private function applyDateFilter($query, $from, $to, $alias = 'o')
    {
        $column = DB::raw("COALESCE($alias.ordered_at, $alias.created_at)");

        // Histórico completo
        if (!$from && !$to) {
            return $query;
        }

        // Solo desde
        if ($from && !$to) {
            return $query->where($column, '>=', $from);
        }

        // Solo hasta
        if (!$from && $to) {
            return $query->where($column, '<=', $to);
        }

        // Rango completo
        return $query->whereBetween($column, [$from, $to]);
    }

    // ================= GENERADOR =================

    public function generate()
    {
        $from = $this->from ? Carbon::parse($this->from)->startOfDay() : null;
        $to   = $this->to   ? Carbon::parse($this->to)->endOfDay()   : null;

        $this->data = match ($this->report) {
            'products'        => $this->products($from, $to),
            'customers'       => $this->customers($from, $to),
            'delivery'        => $this->delivery($from, $to),
            'delivery_detail' => $this->deliveryDetail($from, $to),
            'payments'        => $this->payments($from, $to),
        };
    }

    // ================= REPORTES =================

    private function products($from, $to)
    {
        $query = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->select(
                'p.name',
                DB::raw('SUM(od.quantity) as quantity'),
                DB::raw('SUM(od.subtotal) as total'),
                // 🔥 promedio real ponderado
                DB::raw('SUM(od.subtotal) / NULLIF(SUM(od.quantity), 0) as avg_price')
            )
            ->where('o.status_id', self::STATUS_DELIVERED);

        $this->applyDateFilter($query, $from, $to, 'o');

        return $query
            ->groupBy('p.name')
            ->orderByDesc('quantity')
            ->get();
    }

    private function customers($from, $to)
    {
        $query = DB::table('orders as o')
            ->join('customers as c', 'c.id', '=', 'o.customer_id')
            ->select(
                'c.name',
                'c.email',
                DB::raw('COUNT(o.id) as orders'),
                DB::raw('SUM(o.total) as total'),
                DB::raw('AVG(o.total) as avg')
            )
            ->where('o.status_id', self::STATUS_DELIVERED);

        $this->applyDateFilter($query, $from, $to, 'o');

        return $query
            ->groupBy('c.name', 'c.email')
            ->orderByDesc('orders')
            ->get();
    }

    private function delivery($from, $to)
    {
        $query = DB::table('orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.delivery_user_id')
            ->select(
                DB::raw('COALESCE(u.name, "Sin asignar") as delivery'),
                'o.payment_method',
                DB::raw('COUNT(o.id) as orders'),
                DB::raw('SUM(o.total) as total')
            )
            ->where('o.status_id', self::STATUS_DELIVERED)

            ->whereNotNull('o.delivery_user_id');

        $this->applyDateFilter($query, $from, $to, 'o');

        return $query
            ->groupBy('delivery', 'o.payment_method') // 🔥 clave
            ->orderByDesc('orders')
            ->get();
    }

    private function deliveryDetail($from, $to)
    {
        $query = DB::table('orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.delivery_user_id')
            ->join('customers as c', 'c.id', '=', 'o.customer_id')
            ->select(
                'o.id',
                DB::raw('COALESCE(u.name, "Sin asignar") as delivery'),
                'c.name as customer',
                'o.total',
                'o.payment_method',
                DB::raw('COALESCE(o.ordered_at, o.created_at) as date')
            )
            ->where('o.status_id', self::STATUS_DELIVERED)

            // 🔥 CLAVE: excluir históricos sin domiciliario
            ->whereNotNull('o.delivery_user_id');

        $this->applyDateFilter($query, $from, $to, 'o');

        $orders = $query
            ->orderBy('delivery')
            ->orderByDesc('date')
            ->get();

        return $orders->groupBy('delivery');
    }

    private function payments($from, $to)
    {
        $query = DB::table('orders as o')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(o.total) as total')
            )
            ->where('o.status_id', self::STATUS_DELIVERED);

        $this->applyDateFilter($query, $from, $to, 'o');

        return $query
            ->groupBy('payment_method')
            ->orderByDesc('orders')
            ->get();
    }

    // ================= EXPORT =================

    public function export()
    {
        return match ($this->report) {

            'products' => Excel::download(
                new ProductsReportExport($this->data),
                $this->fileName('productos')
            ),

            'customers' => Excel::download(
                new CustomersReportExport($this->data),
                $this->fileName('clientes')
            ),

            'delivery' => Excel::download(
                new DeliveryReportExport($this->data),
                $this->fileName('domiciliarios')
            ),

            'payments' => Excel::download(
                new PaymentReportExport($this->data),
                $this->fileName('pagos')
            ),
        };
    }

    private function fileName($prefix)
    {
        $date = now()->format('Ymd_His');
        $random = Str::upper(Str::random(4));

        return "{$prefix}_{$date}_{$random}.xlsx";
    }

    public function render()
    {
        return view('livewire.reports.reports-module');
    }
}