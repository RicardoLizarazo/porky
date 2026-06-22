<?php

namespace App\Livewire;

use App\Models\ProductRule;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProductRulesTable extends DataTableComponent
{
    protected $model = ProductRule::class;

    protected $listeners = [
        'refresh-table' => '$refresh'
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

            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Regla', 'name')
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => '

                    <div>
                        <div class="font-weight-bold">
                            '.$row->name.'
                        </div>

                        '.($row->description ? '

                            <div class="small text-muted">
                                '.$row->description.'
                            </div>

                        ' : '').'
                    </div>

                ')
                ->html(),

            Column::make('Costo Extra', 'extra_price')
                ->sortable()
                ->format(fn($value) =>

                    '$ '.number_format($value, 0, ',', '.')

                ),

            Column::make('Estado', 'is_active')
                ->sortable()
                ->format(fn($value) =>

                    $value
                        ? '<span class="badge badge-success">Activa</span>'
                        : '<span class="badge badge-danger">Inactiva</span>'

                )
                ->html(),

            Column::make('Creación', 'created_at')
                ->sortable()
                ->format(fn($value, $row) =>

                    $row->created_at->format('d/m/Y H:i')

                ),

            Column::make('Acciones')
                ->label(fn($row) =>

                    view(
                        'restaurante.product-rules.actions',
                        ['rule' => $row]
                    )

                ),
        ];
    }

    public function builder(): Builder
    {
        return ProductRule::query();
    }
}