<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\StatusOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class OrdersTable extends DataTableComponent
{
    protected $model = Order::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc')
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

            Column::make("ID", "id")
                ->sortable(),

            Column::make("Cliente", "customer_id")
                ->label(function($row) {
                    $customer = $row->customer;

                    $name = $customer?->name ?? 'N/A';
                    $address = $customer?->full_address ?? 'Sin dirección';

                    return '
                        <strong>' . e($name) . '</strong><br>
                        <small class="text-muted">' . e($address) . '</small>
                    ';
                })
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {

                    $cleanSearch = preg_replace('/\D/', '', $searchTerm);

                    $query->orWhereHas('customer', function ($q) use ($searchTerm, $cleanSearch) {

                        $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%");

                        if (!empty($cleanSearch)) {
                            $q->orWhere('telephone', 'like', "%{$cleanSearch}%");
                        }

                    });

                }),

            // TIPO PEDIDO
            Column::make("Tipo")
                ->label(function ($row) {
                    // Pedido web: comportamiento actual
                    if (! $row->table_id) {
                        return e($row->type?->name ?? 'N/A');
                    }

                    // Pedido de mesa: mesa + piso
                    $mesa = $row->diningTable?->name ?? 'Mesa ' . $row->table_id;
                    $piso = $row->floor?->name;

                    $html = '<strong><i class="fas fa-utensils mr-1"></i>' . e($mesa) . '</strong>';

                    if ($piso) {
                        $html .= '<br><small class="text-muted">' . e($piso) . '</small>';
                    }

                    return $html;
                })
                ->html(),

            // TOTAL
            Column::make("Total")
                ->label(fn($row) => '$ ' . number_format($row->total, 0, ',', '.'))
                ->sortable(),

            // ITEMS (desde relacion)
            Column::make("Items")
            ->label(fn($row) => $row->total_items ?? 0),

            // DOMICILIARIO
            Column::make("Domiciliario")
                ->label(fn($row) => $row->delivery_name),

            // ESTADO (YA TIENES ACCESSOR)
            Column::make("Estado")
                ->label(fn($row) => 
                    '<span class="badge badge-' . $row->status_badge['color'] . '">' 
                    . $row->status_badge['name'] . 
                    '</span>'
                )
                ->html(),

            // FECHA DE CREACION
            Column::make("Creado", "created_at")
                ->format(function ($value, $row) {
                    if (! $row->created_at) {
                        return '<small class="text-muted">Sin registro</small>';
                    }

                    return $row->created_at->format('d/m/Y H:i');
                })
                ->html()
                ->sortable(),

            // ACCIONES
            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    view('orders.actions', compact('row'))->render()
                )
                ->html(),
        ];
    }

    public function query(): Builder
    {
        return Order::query()
            ->select('orders.*')
            ->with([
                'customer',
                'status',
                'delivery',
                'type',
            ])
            ->withCount('details');
    }

    public function filters(): array
    {
        return [

            SelectFilter::make('Estado')
                ->options(['' => 'Todos'] + StatusOrder::pluck('name', 'id')->toArray())
                ->filter(fn($builder, $value) => $value ? $builder->where('status_id', $value) : null),

            SelectFilter::make('Domiciliario')
                ->options(['' => 'Todos'] + User::pluck('name', 'id')->toArray())
                ->filter(fn($builder, $value) => $value ? $builder->where('delivery_user_id', $value) : null),

            DateFilter::make('Desde')
                ->filter(fn($builder, $value) => $value ? $builder->whereDate('ordered_at', '>=', $value) : null),

            DateFilter::make('Hasta')
                ->filter(fn($builder, $value) => $value ? $builder->whereDate('ordered_at', '<=', $value) : null),
                
            SelectFilter::make('Origen', 'origin')
                ->options([
                    ''     => 'Todos',
                    'web'  => 'Web / Domicilio',
                    'mesa' => 'Mesas',
                ])
                ->setFilterDefaultValue('web')
                ->filter(function (Builder $builder, string $value) {
                    match ($value) {
                        'mesa'  => $builder->whereNotNull('orders.table_id'),
                        'web'   => $builder->whereNull('orders.table_id'),
                        default => null,
                    };
                }),
        ];
    }
    
}
