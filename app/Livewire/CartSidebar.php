<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CartSidebar extends Component
{
    protected $listeners = [
        'cart-updated' => '$refresh',
        'open-cart' => 'open',
        'close-cart' => 'close',
    ];

    public $open = false;

    public function open()
    {
        $this->open = true;
        $this->dispatch('cart-opened');
    }

    public function close()
    {
        $this->open = false;
        $this->dispatch('cart-closed');
    }

    public function getCartProperty()
    {
        return collect(session()->get('cart', []));
    }

    public function increment($id)
    {
        $this->dispatch('cart-add', $id);
    }

    public function decrement($id)
    {
        $this->dispatch('cart-remove', $id);
    }

    public function getTotalProperty()
    {
        return $this->cart->sum(fn($i) => $i['price'] * $i['quantity']);
    }

    public function render()
    {
        return view('livewire.cart-sidebar');
    }
}
