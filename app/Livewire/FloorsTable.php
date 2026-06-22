<?php

namespace App\Livewire;

use App\Models\Floor;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class FloorsTable extends DataTableComponent
{
    protected $model = Floor::class;

    protected $listeners = ['refresh-table' => '$refresh'];

    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN
    |--------------------------------------------------------------------------
    */

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('sort_order', 'asc')
            ->setPerPage(25)
            ->setSearchEnabled()
            ->setTableWrapperAttributes([
                'class' => 'table-responsive shadow-sm'
            ])
            ->setTdAttributes(fn () => [
                'class' => 'align-middle'
            ])
            ->setThAttributes(fn () => [
                'class' => 'text-uppercase small'
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | COLUMNAS
    |--------------------------------------------------------------------------
    */

    public function columns(): array
    {
        return [

            Column::make("ID", "id")
                ->sortable()
                ->searchable(),

            Column::make("Piso", "name")
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => '
                    <div>
                        <div class="font-weight-bold">'.$row->name.'</div>

                        <div class="text-muted small">
                            Orden: '.$row->sort_order.'
                        </div>
                    </div>
                ')
                ->html(),

            Column::make("Ubicación", "location.name")
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => '
                    <span class="badge badge-info">
                        '.$row->location?->name.'
                    </span>
                ')
                ->html(),

            Column::make("Mesas")
                ->label(fn($row) => $row->tables_count)
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy('tables_count', $direction);
                }),

            Column::make("Estado", "is_active")
                ->sortable()
                ->format(fn($value) =>
                    $value
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>'
                )
                ->html(),

            Column::make("Fecha creación", "created_at")
                ->sortable()
                ->format(fn($value, $row) =>
                    $row->created_at->format('d/m/Y H:i')
                ),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    '<div class="table-actions">' .
                    view('restaurante.floors.actions', [
                        'floor' => $row
                    ]) .
                    '</div>'
                )
                ->html(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    public function query(): Builder
    {
        return Floor::query()
            ->with('location')
            ->withCount('tables');
    }
}