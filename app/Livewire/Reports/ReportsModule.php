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

    public $report = 'products'; // default

    public $data = [];

    const STATUS_DELIVERED = 5;

    public function mount($type = 'products')
    {
        $this->report = $type;

        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to   = now()->format('Y-m-d');

        $this->generate();
    }

    public function updated($field)
    {
        if (in_array($field, ['from', 'to', 'report'])) {
            $this->generate();
        }
    }

    public function generate()
    {
        $from = Carbon::parse($this->from)->startOfDay();
        $to   = Carbon::parse($this->to)->endOfDay();

        $this->data = match ($this->report) {
            'products'  => $this->products($from, $to),
            'customers' => $this->customers($from, $to),
            'delivery'  => $this->delivery($from, $to),
            'payments'  => $this->payments($from, $to),
        };
    }

    // ================= REPORTES =================

    private function products($from, $to)
    {
        return DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->select(
                'p.name',
                DB::raw('SUM(od.quantity) as quantity'),
                DB::raw('SUM(od.subtotal) as total'),
                DB::raw('AVG(od.price) as avg_price')
            )
            ->where('o.status_id', self::STATUS_DELIVERED)
            ->whereBetween('o.created_at', [$from, $to])
            ->groupBy('p.name')
            ->orderByDesc('quantity')
            ->get();
    }

    private function customers($from, $to)
    {
        return DB::table('orders as o')
            ->join('customers as c', 'c.id', '=', 'o.customer_id')
            ->select(
                'c.name',
                'c.email',
                DB::raw('COUNT(o.id) as orders'),
                DB::raw('SUM(o.total) as total'),
                DB::raw('AVG(o.total) as avg')
            )
            ->where('o.status_id', self::STATUS_DELIVERED)
            ->whereBetween('o.created_at', [$from, $to])
            ->groupBy('c.name', 'c.email')
            ->orderByDesc('orders')
            ->get();
    }

    private function delivery($from, $to)
    {
        return DB::table('orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.delivery_user_id')
            ->select(
                DB::raw('COALESCE(u.name, "Sin asignar") as delivery'),
                DB::raw('COUNT(o.id) as orders'),
                DB::raw('SUM(o.total) as total')
            )
            ->where('o.status_id', self::STATUS_DELIVERED)
            ->whereBetween('o.created_at', [$from, $to])
            ->groupBy('delivery')
            ->orderByDesc('orders')
            ->get();
    }

    private function payments($from, $to)
    {
        return DB::table('orders')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as total')
            )
            ->where('status_id', self::STATUS_DELIVERED)
            ->whereBetween('created_at', [$from, $to])
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
        $random = Str::upper(Str::random(4)); // corto pero suficiente

        return "{$prefix}_{$date}_{$random}.xlsx";
    }

    public function render()
    {
        return view('livewire.reports.reports-module');
    }
}
