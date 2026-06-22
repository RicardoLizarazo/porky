<?php

namespace App\Livewire;

use App\Models\DiningTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TablesTable extends DataTableComponent
{
    protected $model = DiningTable::class;

    protected $listeners = ['refresh-table' => '$refresh'];

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

            Column::make("Mesa", "name")
                ->sortable()
                ->searchable(),

            Column::make("Ubicación", "location.name")
                ->sortable()
                ->searchable(),

            Column::make("Piso", "floor.name")
                ->sortable()
                ->searchable(),

            Column::make("Capacidad", "capacity")
                ->sortable(),

            Column::make("Estado", "status")
                ->format(fn($value) => match($value) {

                    'available'
                        => '<span class="badge badge-success">Disponible</span>',

                    'occupied'
                        => '<span class="badge badge-danger">Ocupada</span>',

                    'reserved'
                        => '<span class="badge badge-warning">Reservada</span>',

                    'cleaning'
                        => '<span class="badge badge-info">Limpieza</span>',

                    'pending_payment'
                        => '<span class="badge badge-dark">Pago Pendiente</span>',

                    default
                        => $value
                })
                ->html(),

            Column::make("Activa", "is_active")
                ->format(fn($value) =>
                    $value
                        ? '<span class="badge badge-success">Sí</span>'
                        : '<span class="badge badge-danger">No</span>'
                )
                ->html(),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    '<div class="table-actions">' .
                    view('restaurante.tables.actions', [
                        'table' => $row
                    ]) .
                    '</div>'
                )
                ->html(),
        ];
    }

    public function query(): Builder
    {
        return DiningTable::query()
            ->with(['location', 'floor']);
    }
}