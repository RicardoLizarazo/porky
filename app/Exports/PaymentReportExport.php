<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentReportExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data)->map(fn($row) => [
            'Metodo de pago' => $row->payment_method,
            'Pedidos' => $row->orders,
            'Total' => $row->total,
        ]);
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Metodo de pago',
            'Pedidos',
            'Total',
        ];
    }
}
