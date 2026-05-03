<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ProductHome extends Component
{
    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.product-home', [
            'products' => Product::with(['variants'])->latest()->take(8)->get()
        ]);
    }
}