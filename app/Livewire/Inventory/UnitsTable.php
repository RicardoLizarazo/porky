<?php

namespace App\Livewire\Inventory;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UnitsTable extends DataTableComponent
{
    protected $model = Unit::class;

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

            Column::make("Nombre", "name")
                ->sortable()
                ->searchable(),

            Column::make("Abreviatura", "abbreviation")
                ->sortable()
                ->searchable(),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    view('inventory.units.actions', ['unit' => $row])
                )->html(),
        ];
    }

    public function builder(): Builder
    {
        return Unit::query()->select('units.*');
    }
}
