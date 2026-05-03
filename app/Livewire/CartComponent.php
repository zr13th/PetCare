<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;
use Livewire\Attributes\On;

class CartComponent extends Component
{

    public function mount(): void
    {
        if (auth()->check()) {
            try {
                Cart::restore(auth()->id());
            } catch (\Exception) {
            }
        }
    }

    #[On('addToCart')]
    public function addToCart(int $variantId, int $quantity = 1): void
    {
        $variant = ProductVariant::with([
            'product',
            'attributeValues',   // ← dùng quan hệ belongsToMany đã sửa
        ])->findOrFail($variantId);

        $existing = Cart::search(fn($item) => $item->id === $variantId);

        if ($existing->isNotEmpty()) {
            $row = $existing->first();
            Cart::update($row->rowId, $row->qty + $quantity);
        } else {
            Cart::add($variant, $quantity)->associate(ProductVariant::class);
        }

            // Debug
        \Log::info('Cart content:', Cart::content()->toArray());
        \Log::info('Auth check:', ['logged_in' => auth()->check(), 'user_id' => auth()->id()]);

        $this->persistCart();
        $this->dispatch('cart-updated');
    }

    public function updateQty(string $rowId, int $qty): void
    {
        if ($qty <= 0) {
            $this->removeItem($rowId);
            return;
        }
        Cart::update($rowId, $qty);
        $this->persistCart();
        $this->dispatch('cart-updated');
    }

    public function removeItem(string $rowId): void
    {
        Cart::remove($rowId);
        $this->persistCart();
        $this->dispatch('cart-updated');
    }

    public function clearCart(): void
    {
        Cart::destroy();
        if (auth()->check()) {
            try {
                Cart::erase(auth()->id());
            } catch (\Exception) {}
        }
        $this->dispatch('cart-updated');
    }

    private function persistCart(): void
    {
        if (!auth()->check()) return;

        try {
            Cart::erase(auth()->id());
            \Log::info('Cart erased');
        } catch (\Exception $e) {
            \Log::error('Erase error: ' . $e->getMessage());
        }

        try {
            Cart::store(auth()->id());
            \Log::info('Cart stored successfully');
        } catch (\Exception $e) {
            \Log::error('Store error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.cart-component', [
            'cartItems' => Cart::content(),
            'total'     => Cart::total(0, ',', '.'),
            'count'     => Cart::count(),
        ]);
    }
}