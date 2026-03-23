<?php

namespace App\Livewire;

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class LocationsTable extends DataTableComponent
{
    protected $model = Location::class;

    protected $listeners = ['refresh-table' => '$refresh'];

    /**
     * Configuración general
     */
    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'asc')
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

    /**
     * Columnas
     */
    public function columns(): array
    {
        return [

            Column::make("ID", "id")
                ->sortable()
                ->searchable(),

            Column::make("Ubicación", "name")
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => '
                    <div>
                        <div class="font-weight-bold">'.$row->name.'</div>
                        '.($row->description ? '
                            <div class="text-muted small">'.$row->description.'</div>
                        ' : '').'
                    </div>
                ')->html(),

            Column::make("Estado", "is_active")
                ->sortable()
                ->format(fn($value) =>
                    $value
                        ? '<span class="badge badge-success">Activa</span>'
                        : '<span class="badge badge-danger">Inactiva</span>'
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
                    view('restaurante.locations.actions', ['location' => $row]) .
                    '</div>'
                )
                ->html(),
        ];
    }

    /**
     * Query base
     */
    public function query(): Builder
    {
        return Location::query();
    }
}