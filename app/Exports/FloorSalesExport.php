<?php
// app/Exports/FloorSalesExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FloorSalesExport implements FromCollection, WithHeadings, WithStyles
{
    public function __construct(
        protected ?int $locationId,
        protected ?int $floorId,
        protected string $dateFrom,
        protected string $dateTo,
    ) {}

    public function collection()
    {
        return DB::table('orders')
            ->leftJoin('floors', 'orders.floor_id', '=', 'floors.id')
            ->select(
                'floors.name as Piso',
                DB::raw('COUNT(orders.id) as Pedidos'),
                DB::raw('SUM(orders.total) as Total')
            )
            ->where('orders.is_paid', 1)
            ->when($this->locationId, fn($q) => $q->where('orders.location_id', $this->locationId))
            ->when($this->floorId, fn($q) => $q->where('orders.floor_id', $this->floorId))
            ->whereBetween('orders.created_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo . ' 23:59:59',
            ])
            ->groupBy('floors.id', 'floors.name')
            ->orderByDesc('Total')
            ->get();
    }

    public function headings(): array
    {
        return ['Piso', 'Pedidos', 'Total'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}