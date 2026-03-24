<?php

namespace App\Livewire;

use Livewire\Component;

class CartFloating extends Component
{
    public $open = false;

    protected $listeners = [
        'cart-updated' => '$refresh',
        'cart-opened' => 'hide',
        'cart-closed' => 'show',
    ];

    public function openCart()
    {
        $this->dispatch('open-cart');
    }

    public function getCartProperty()
    {
        return collect(session()->get('cart', []));
    }

    public function getCountProperty()
    {
        return $this->cart->sum('quantity');
    }

    public function getTotalProperty()
    {
        return $this->cart->sum(fn($i) => $i['price'] * $i['quantity']);
    }

    public function render()
    {
        return view('livewire.cart-floating');
    }

    public function hide()
    {
        $this->open = true;
    }

    public function show()
    {
        $this->open = false;
    }
}
