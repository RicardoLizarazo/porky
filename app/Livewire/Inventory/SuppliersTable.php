<?php

namespace App\Livewire\Inventory;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SuppliersTable extends DataTableComponent
{
    protected $model = Supplier::class;

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

            Column::make("Proveedor", "name")
                ->searchable()
                ->sortable()
                ->format(fn($value, $row) => '
                    <div>
                        <strong>'.e($row->name).'</strong><br>
                        <small>'.e($row->contact_name).'</small>
                    </div>
                ')->html(),

            Column::make("Teléfono", "phone")->searchable(),

            Column::make("Productos")
                ->label(fn($row) => $row->inventoryItems()->count()),

            Column::make("Estado", "status")
                ->sortable()
                ->format(fn($value) =>
                    $value
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>'
                )->html(),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    view('inventory.suppliers.actions', ['supplier' => $row])
                )->html(),
        ];
    }

    public function builder(): Builder
    {
        return Supplier::query()->select('suppliers.*');
    }
}
