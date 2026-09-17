<?php

namespace App\Livewire\Inventory;

use App\Models\PurchaseReturn;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PurchaseReturnsTable extends DataTableComponent
{
    protected $model = PurchaseReturn::class;

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
                ->label(fn($row) => e($row->supplier->name)),

            Column::make("Factura original")
                ->label(fn($row) => e($row->purchase->invoice_number)),

            Column::make("Fecha", "return_date")
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y')),

            Column::make("Nota crédito", "credit_note_number"),

            Column::make("Total", "total")
                ->sortable()
                ->format(fn($value) => '$' . number_format($value, 2)),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    view('inventory.purchase-returns.actions', ['purchaseReturn' => $row])
                )->html(),
        ];
    }

    public function builder(): Builder
    {
        return PurchaseReturn::query()->select('purchase_returns.*')->with('supplier', 'purchase');
    }
}
