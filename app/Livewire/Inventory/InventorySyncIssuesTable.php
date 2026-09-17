<?php

namespace App\Livewire\Inventory;

use App\Models\InventorySyncIssue;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class InventorySyncIssuesTable extends DataTableComponent
{
    protected $model = InventorySyncIssue::class;

    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('last_seen_at', 'desc')
            ->setPerPage(25);
    }

    public function columns(): array
    {
        return [
            Column::make("Producto")
                ->label(fn($row) => e($row->product->name ?? 'Producto eliminado')),

            Column::make("Motivo", "reason")
                ->format(fn($value) =>
                    $value === 'unlinked_product'
                        ? 'Producto sin vincular a inventario'
                        : e($value)
                ),

            Column::make("Veces detectado", "occurrences")
                ->sortable(),

            Column::make("Última venta", "last_seen_at")
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y H:i')),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    view('inventory.sync-issues.actions', ['issue' => $row])
                )->html(),
        ];
    }

    public function builder(): Builder
    {
        return InventorySyncIssue::query()
            ->select('inventory_sync_issues.*')
            ->pending()
            ->with('product');
    }
}
