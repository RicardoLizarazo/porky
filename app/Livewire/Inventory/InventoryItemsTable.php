<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class InventoryItemsTable extends DataTableComponent
{
    protected $model = InventoryItem::class;

    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('name', 'asc')
            ->setPerPage(25)
            ->setSearchEnabled();
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")->sortable(),

            Column::make("Producto", "name")
                ->searchable()
                ->sortable()
                ->format(fn($value, $row) => '
                    <div>
                        <strong>'.e($row->name).'</strong><br>
                        <small class="text-muted">'.e($row->code ?? 'Sin código').'</small>
                    </div>
                ')->html(),

            Column::make("Tipo", "type")
                ->sortable()
                ->format(fn($value, $row) => e($row->type_label)),

            Column::make("Unidad base")
                ->label(fn($row) => $row->baseUnit->abbreviation ?? '-'),

            Column::make("Stock", "stock")
                ->sortable()
                ->format(fn($value, $row) =>
                    number_format($value, 2) . ' ' . ($row->baseUnit->abbreviation ?? '')
                ),

            Column::make("Costo promedio", "average_cost")
                ->sortable()
                ->format(fn($value) => '$' . number_format($value, 2)),

            Column::make("Valor")
                ->label(fn($row) => '$' . number_format($row->value, 2)),

            Column::make("Estado", "is_active")
                ->format(fn($value) =>
                    $value
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>'
                )->html(),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    view('inventory.inventory-items.actions', ['item' => $row])
                )->html(),
        ];
    }

    public function builder(): Builder
    {
        return InventoryItem::query()->select('inventory_items.*')->with('baseUnit');
    }
}
