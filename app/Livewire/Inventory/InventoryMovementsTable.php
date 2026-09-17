<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class InventoryMovementsTable extends DataTableComponent
{
    protected $model = InventoryMovement::class;

    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc')
            ->setPerPage(25);
    }

    public function columns(): array
    {
        return [
            Column::make("Fecha", "created_at")
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y H:i')),

            Column::make("Producto")
                ->label(fn($row) => e($row->inventoryItem->name)),

            Column::make("Tipo", "type")
                ->format(fn($value, $row) => e($row->type_label)),

            Column::make("Cantidad", "quantity")
                ->format(fn($value, $row) =>
                    ($value >= 0 ? '+' : '') . number_format($value, 2) . ' ' . ($row->inventoryItem->baseUnit->abbreviation ?? '')
                ),

            Column::make("Costo unitario", "unit_cost")
                ->format(fn($value) => $value !== null ? '$' . number_format($value, 2) : '-'),

            Column::make("Saldo", "balance_after")
                ->format(fn($value, $row) => number_format($value, 2) . ' ' . ($row->inventoryItem->baseUnit->abbreviation ?? '')),

            Column::make("Nota", "reason_note"),

            Column::make("Registrado por")
                ->label(fn($row) => e($row->createdBy->name ?? '-')),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Producto', 'inventory_item_id')
                ->options(['' => 'Todos'] + InventoryItem::pluck('name', 'id')->toArray())
                ->filter(fn($builder, $value) => $value ? $builder->where('inventory_item_id', $value) : null),

            SelectFilter::make('Tipo', 'type')
                ->options(['' => 'Todos'] + InventoryMovement::$types)
                ->filter(fn($builder, $value) => $value ? $builder->where('type', $value) : null),

            DateFilter::make('Desde')
                ->filter(fn($builder, $value) => $value ? $builder->whereDate('created_at', '>=', $value) : null),

            DateFilter::make('Hasta')
                ->filter(fn($builder, $value) => $value ? $builder->whereDate('created_at', '<=', $value) : null),
        ];
    }

    public function builder(): Builder
    {
        return InventoryMovement::query()->select('inventory_movements.*')->with('inventoryItem.baseUnit', 'createdBy');
    }
}
