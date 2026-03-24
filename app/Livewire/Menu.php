<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Location;

class Menu extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = null;
    public $perPage = 8;
    public $location_id = 1;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $location = Location::find($this->location_id);

        $isOpen = $location?->isOpenNow() ?? false;

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where('is_visible', true)
            ->when($this->category_id, fn($q) =>
                $q->where('category_id', $this->category_id)
            )
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%'.$this->search.'%')
            )
            ->paginate($this->perPage);

        $categories = Category::where('is_active', true)->get();

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

    public function showProduct($id)
    {
        $product = Product::findOrFail($id);

        $this->dispatch('product-data', [
            'name' => $product->name,
            'description' => $product->description,
            'image' => $product->image 
                ? asset('storage/'.$product->image)
                : asset('images/no-image.png'),
            'price' => $product->price,
        ]);
    }
}