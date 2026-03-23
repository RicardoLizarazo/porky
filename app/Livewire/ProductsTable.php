<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProductsTable extends DataTableComponent
{
    protected $model = Product::class;

    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('products.name', 'asc')
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

            // 🔥 PRODUCTO 
            Column::make("Producto")
                ->label(fn($row) => $row->product_info)
                ->html()
                ->sortable(
                    fn(Builder $query, string $direction) =>
                    $query->orderBy('products.name', $direction)
                )
                ->searchable(
                    fn(Builder $query, string $searchTerm) =>
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('products.name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('products.code', 'like', '%' . $searchTerm . '%');
                    })
                ),

            // 📂 Categoría
            Column::make("Categoría", "category.name")
                ->sortable()
                ->searchable(),

            // 💲 Precio
            Column::make("Precio", "price")
                ->sortable()
                ->format(fn($value) => '$ ' . number_format($value, 0, ',', '.')),

            // 📦 Empaque
            Column::make("Empaque", "packaging_cost")
                ->sortable()
                ->format(fn($value) => $value > 0 
                    ? '$ ' . number_format($value, 0, ',', '.') 
                    : '<span class="text-muted">Sin costo</span>')
                ->html(),

            // 👁️ Visible
            Column::make("Visible", "is_visible")
                ->format(fn($value) => $value 
                    ? '<span class="badge badge-info">Sí</span>' 
                    : '<span class="badge badge-secondary">No</span>')
                ->html(),

            // 🔥 Estado
            Column::make("Estado", "is_active")
                ->sortable()
                ->format(fn($value) => $value 
                    ? '<span class="badge badge-success">Activo</span>' 
                    : '<span class="badge badge-danger">Inactivo</span>')
                ->html(),

            // 📅 Fecha
            Column::make("Creado", "created_at")
                ->sortable()
                ->format(fn($value) => $value ? $value->format('d/m/Y') : ''),

            // ⚙️ Acciones
            Column::make("Acciones", "id")
                ->format(function($value, $row) {
                    return '<div class="table-actions">' . 
                           view('restaurante.products.actions', ['product' => $row])->render() . 
                           '</div>';
                })
                ->html(),
        ];
    }

    /**
     * Query optimizada
     */
    public function query(): Builder
    {
        return Product::query()
            ->with('category')
            ->select('products.*'); // 🔥 Especificar products.* para evitar ambigüedad
    }
}