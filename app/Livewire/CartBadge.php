<?php

namespace App\Livewire;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;
use Livewire\Attributes\On;

class CartBadge extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->count = Cart::count();
    }

    #[On('cart-updated')]
    #[On('addToCart')]
    public function refresh(): void
    {
        $this->count = Cart::count();
    }

    public function render()
    {
        return view('livewire.cart-badge');
    }
}