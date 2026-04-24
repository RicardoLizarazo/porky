<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersReportExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data)->map(function ($row) {
            return [
                'Cliente' => $row->name,
                'Email' => $row->email,
                'Pedidos' => $row->orders,
                'Total' => $row->total,
                'Promedio' => $row->avg,
            ];
        });
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'Email',
            'Pedidos',
            'Total',
            'Promedio',
        ];
    }
}
