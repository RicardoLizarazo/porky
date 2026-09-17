<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PriceHistoryTable extends DataTableComponent
{
    protected $model = PurchaseDetail::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc')
            ->setPerPage(25);
    }

    public function columns(): array
    {
        return [
            Column::make("Fecha")
                ->label(fn($row) => $row->purchase->purchase_date->format('d/m/Y')),

            Column::make("Factura")
                ->label(fn($row) => e($row->purchase->invoice_number)),

            Column::make("Proveedor")
                ->label(fn($row) => e($row->purchase->supplier->name)),

            Column::make("Producto")
                ->label(fn($row) => e($row->inventoryItem->name)),

            Column::make("Cantidad")
                ->label(fn($row) => number_format($row->quantity, 2) . ' ' . ($row->unit->abbreviation ?? '')),

            Column::make("Costo por unidad de compra")
                ->label(fn($row) => '$' . number_format($row->unit_cost, 2)),

            Column::make("Costo por unidad base")
                ->label(fn($row) => '$' . number_format($row->base_quantity > 0 ? $row->subtotal / $row->base_quantity : 0, 2)),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Producto', 'inventory_item_id')
                ->options(['' => 'Todos'] + InventoryItem::pluck('name', 'id')->toArray())
                ->filter(fn($builder, $value) => $value ? $builder->where('purchase_details.inventory_item_id', $value) : null),

            SelectFilter::make('Proveedor', 'supplier_id')
                ->options(['' => 'Todos'] + Supplier::pluck('name', 'id')->toArray())
                ->filter(fn($builder, $value) => $value ? $builder->where('purchases.supplier_id', $value) : null),

            DateFilter::make('Desde')
                ->filter(fn($builder, $value) => $value ? $builder->whereDate('purchases.purchase_date', '>=', $value) : null),

            DateFilter::make('Hasta')
                ->filter(fn($builder, $value) => $value ? $builder->whereDate('purchases.purchase_date', '<=', $value) : null),
        ];
    }

    public function builder(): Builder
    {
        return PurchaseDetail::query()
            ->join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->where('purchases.status', 'confirmed')
            ->select('purchase_details.*')
            ->with('purchase.supplier', 'inventoryItem', 'unit');
    }
}
