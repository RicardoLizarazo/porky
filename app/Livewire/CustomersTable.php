<?php

namespace App\Livewire;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CustomersTable extends DataTableComponent
{
    protected $model = Customer::class;

    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('name', 'asc')
            ->setPerPage(25);
    }

    public function columns(): array
    {
        return [

            Column::make("ID", "id")->sortable(),

            Column::make("Nombre", "name")
                ->searchable()
                ->format(fn($value, $row) => '
                    <div>
                        <strong>'.$row->name.'</strong><br>
                        <small>'.$row->email.'</small>
                    </div>
                ')->html(),

            Column::make("Teléfono", "telephone")
                ->searchable(),

            Column::make("Estado", "status")
                ->format(fn($value) =>
                    $value
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>'
                )->html(),

            Column::make("Fecha", "created_at")
                ->format(fn($value) => $value->format('d/m/Y')),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    view('customers.actions', ['customer' => $row])
                )->html(),
        ];
    }

    public function query(): Builder
    {
        return Customer::query();
    }
}