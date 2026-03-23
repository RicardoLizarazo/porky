<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Activitylog\Models\Activity;

class AuditLogsTable extends DataTableComponent
{
    protected $model = Activity::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('name', 'asc')
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
            Column::make("ID", "id")->sortable(),
            Column::make("Descripción", "description")->sortable(),
            Column::make("Usuario", "causer_id"),
            Column::make("Propiedades", "properties"),
            Column::make("Fecha", "created_at")->sortable(),
        ];
    }
}
