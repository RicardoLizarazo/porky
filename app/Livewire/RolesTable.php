<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Permission\Models\Role;

class RolesTable extends DataTableComponent
{
    protected $model = Role::class;

    protected $listeners = ['refresh-table' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'asc')
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

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable()
                ->searchable()
                ->hideIf(false),

            Column::make("Rol", "name")
                ->sortable()
                ->searchable(),

            Column::make("Descripción", "description")
                ->sortable()
                ->searchable(),

            Column::make("Fecha creación", "created_at")
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) => $row->created_at->format('d/m/Y H:i')
                ),
            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    '<div class="table-actions">'.
                    view('security.roles.actions', ['user' => $row]).
                    '</div>'
                )
                ->html(),
        ];
    }
}