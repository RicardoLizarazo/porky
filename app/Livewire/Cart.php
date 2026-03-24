<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class Cart extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function render()
    {
        return view('livewire.cart');
    }

    protected $listeners = [
        'cart-add' => 'add',
        'cart-remove' => 'remove',
    ];

    /*
    |--------------------------------------------------------------------------
    | ADD
    |--------------------------------------------------------------------------
    */
    public function add($id)
    {
        $product = Product::findOrFail($id);

        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity']++;
        } else {
            $this->cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
                'packaging_cost' => $product->packaging_cost ?? 0
            ];
        }

        $this->sync();
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE
    |--------------------------------------------------------------------------
    */
    public function remove($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity']--;

            if ($this->cart[$id]['quantity'] <= 0) {
                unset($this->cart[$id]);
            }
        }

        $this->sync();
    }

    /*
    |--------------------------------------------------------------------------
    | SYNC
    |--------------------------------------------------------------------------
    */
    private function sync()
    {
        session()->put('cart', $this->cart);

        $this->dispatch('cart-updated'); // refresca todo
        $this->dispatch('open-cart');    // abre sidebar automáticamente
        $this->dispatch('cart-opened');
    }

    /*
    |--------------------------------------------------------------------------
    | TOTALS
    |--------------------------------------------------------------------------
    */
    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getPackagingProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['packaging_cost'] * $item['quantity'];
        });
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->packaging;
    }
}