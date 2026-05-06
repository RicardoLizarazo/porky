<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CustomerOrdersTable extends DataTableComponent
{
    protected $model = Order::class;
    public $customerId;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('ordered_at', 'desc')
            ->setPerPage(10);
    }

    public function columns(): array
    {
        return [

            Column::make("ID", "id"),

            Column::make("Total")
                ->label(fn($row) => '$ ' . number_format($row->total, 0, ',', '.')),

            Column::make("Estado")
                ->label(fn($row) =>
                    '<span class="badge badge-' . $row->status_badge['color'] . '">' 
                    . $row->status_badge['name'] . 
                    '</span>'
                )
                ->html(),

            Column::make("Fecha", "ordered_at")
                ->format(fn($value, $row) => $row->ordered_at?->format('d/m/Y H:i')),

            Column::make("Acciones")
                ->label(fn($row) => '
                    <button 
                        class="btn btn-sm btn-outline-primary"
                        onclick="
                            window.dispatchEvent(new CustomEvent(\'toggle-loading\', { detail: true }));
                            Livewire.dispatch(\'view-order\', { id: '.$row->id.' });
                        "
                    >
                        Ver
                    </button>
                ')
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        return Order::query()
            ->where('customer_id', $this->customerId)
            ->with('status');
    }
}