<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class Menu extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = null;
    public $perPage = 8;
    public $location_id = 1;
    public $showPopular = false;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $location = Location::find($this->location_id);
        $isOpen = $location?->isOpenNow() ?? false;

        $user = auth()->user();

        $isAdmin = $user && $user->hasAnyRole(['Administrador', 'Consulta', 'Mesero']);

        // CATEGORÍAS
        $categories = Category::query()
            ->where('is_active', true)

            ->when(
                !$isAdmin,
                fn($q) => $q->where('is_visible', true)
            )

            ->orderBy('name')
            ->get();

        // PRODUCTOS
        $products = Product::query()
            ->with('category')
            ->where('is_active', true)

            // Visibilidad por rol
            ->when(
                !$isAdmin,
                fn($q) => $q->where('is_visible', true)
            )

            // Asegura coherencia con categoría
            ->whereHas('category', function ($q) use ($isAdmin) {
                $q->where('is_active', true);

                if (!$isAdmin) {
                    $q->where('is_visible', true);
                }
            })

            // Filtro por categoría
            ->when($this->category_id, function ($q) {
                $q->where('category_id', $this->category_id);
            })

            // Búsqueda
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })

            ->paginate($this->perPage);

        return view('menu', [
            'products' => $products,
            'categories' => $categories,
            'isOpen' => $isOpen,
        ]);
    }

    public function filterCategory($id)
    {
        $this->resetPage();
        $this->category_id = $id;
        $this->showPopular = false;
    }

    protected $listeners = [
        'cart-updated' => '$refresh',
        'open-cart' => 'open',
        'view-product' => 'showProduct',
    ];

    public function addToCart($id)
    {
        $this->dispatch('cart-add', $id);
    }

    public function decrementCart($id)
    {
        $this->dispatch('cart-remove', $id);
    }

    public function getQty($id)
    {
        $cart = session()->get('cart', []);
        return $cart[$id]['quantity'] ?? 0;
    }

    public function showPopularProducts()
    {
        $this->resetPage();
        $this->category_id = null;
        $this->showPopular = true;
    }
}