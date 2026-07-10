<?php

namespace App\Livewire;

use App\Models\CashRegister;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CashRegistersTable extends DataTableComponent
{
    protected $model = CashRegister::class;

    protected $listeners = [
        'refreshDatatable' => '$refresh'
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id')

            ->setDefaultSort('name', 'asc')

            ->setPerPage(25)

            ->setSearchEnabled()

            ->setTableWrapperAttributes([
                'class' => 'table-responsive shadow-sm'
            ])

            ->setTdAttributes(fn() => [
                'class' => 'align-middle'
            ])

            ->setThAttributes(fn() => [
                'class' => 'text-uppercase small'
            ]);
    }

    public function columns(): array
    {
        return [

            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Caja', 'name')
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => '

                    <div>

                        <div class="font-weight-bold">

                            '.$row->name.'

                        </div>

                    </div>

                ')
                ->html(),

            Column::make('Sede')
                ->label(fn($row) =>
                    $row->location?->name
                )
                ->sortable(),

            Column::make('Piso')
                ->label(fn($row) =>
                    $row->floor?->name
                )
                ->sortable(),

            Column::make('Responsable')
                ->label(fn($row) =>
                    $row->responsible?->name ?? '-'
                )
                ->sortable(),

            Column::make('Estado', 'is_active')

                ->sortable()

                ->format(fn($value) =>

                    $value

                    ? '<span class="badge badge-success">
                        Activa
                       </span>'

                    : '<span class="badge badge-danger">
                        Inactiva
                       </span>'
                )

                ->html(),

            Column::make('Sesiones')
                ->label(fn($row) =>
                    $row->sessions_count
                ),

            Column::make('Fecha creación', 'created_at')
                ->sortable()
                ->format(fn($value, $row) =>

                    $row->created_at
                        ->format('d/m/Y H:i')
                ),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    '<div class="table-actions">' .
                    view('restaurante.cash-registers.actions', ['cashRegister' => $row]) .
                    '</div>'
                )
                ->html(),
        ];
    }

    public function query(): Builder
    {
        return CashRegister::query()

            ->with([
                'location',
                'floor',
                 'responsible',
            ])

            ->withCount([
                'sessions'
            ]);
    }
}