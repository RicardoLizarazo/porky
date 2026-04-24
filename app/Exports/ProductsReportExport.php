<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsReportExport implements FromCollection
{
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data)->map(function ($row) {
            return [
                'Producto' => $row->name,
                'Cantidad' => $row->quantity,
                'Total' => $row->total,
                'Promedio' => $row->avg_price,
            ];
        });
    }

    public function collection()
    {
        return $this->data;
    }
}
