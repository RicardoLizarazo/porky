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
            ->setDefaultSort('ordered_at', 'desc')
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

            // 👤 CLIENTE + DIRECCIÓN
            Column::make("Cliente")
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
                ->searchable(function (Builder $query, string $searchTerm) {
                    $query->whereHas('customer', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%")
                        ->orWhere('telephone', 'like', "%{$searchTerm}%");
                    });
                }),

            // 📦 TIPO PEDIDO
            Column::make("Tipo")
                ->label(fn($row) => $row->type?->name ?? 'N/A'),

            // 💲 TOTAL
            Column::make("Total")
                ->label(fn($row) => '$ ' . number_format($row->total, 0, ',', '.'))
                ->sortable(),

            // 📦 ITEMS (desde relación)
            Column::make("Items")
            ->label(fn($row) => $row->total_items ?? 0),

            // 🚚 DOMICILIARIO
            Column::make("Domiciliario")
                ->label(fn($row) => $row->delivery_name),

            // 🔥 ESTADO (YA TIENES ACCESSOR 🔥)
            Column::make("Estado")
                ->label(fn($row) => 
                    '<span class="badge badge-' . $row->status_badge['color'] . '">' 
                    . $row->status_badge['name'] . 
                    '</span>'
                )
                ->html(),

            // 🕒 FECHA
            Column::make("Fecha", "ordered_at")  // Cambia 'make' a usar el campo directamente
                ->format(fn($value, $row) => $row->ordered_at?->format('d/m/Y H:i'))
                ->sortable(),  // Importante: agregar sortable

            // ⚙️ ACCIONES
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
        ];
    }
    
}
