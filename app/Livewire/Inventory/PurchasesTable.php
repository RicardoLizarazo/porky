<?php

namespace App\Livewire\Inventory;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PurchasesTable extends DataTableComponent
{
    protected $model = Purchase::class;

    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc')
            ->setPerPage(25)
            ->setSearchEnabled();
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")->sortable(),

            Column::make("Proveedor")
                ->label(fn($row) => e($row->supplier->name))
                ->sortable(function (Builder $query, $direction) {
                    return $query->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                        ->orderBy('suppliers.name', $direction)
                        ->select('purchases.*');
                }),

            Column::make("Factura", "invoice_number")->searchable()->sortable(),

            Column::make("Fecha", "purchase_date")
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y')),

            Column::make("Total", "total")
                ->sortable()
                ->format(fn($value) => '$' . number_format($value, 2)),

            Column::make("Estado", "status")
                ->sortable()
                ->format(fn($value) =>
                    $value === 'confirmed'
                        ? '<span class="badge badge-success">Confirmada</span>'
                        : '<span class="badge badge-warning">Borrador</span>'
                )->html(),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    view('inventory.purchases.actions', ['purchase' => $row])
                )->html(),
        ];
    }

    public function builder(): Builder
    {
        return Purchase::query()->select('purchases.*')->with('supplier');
    }
}
