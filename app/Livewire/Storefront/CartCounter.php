<?php

namespace App\Livewire\Storefront;

use App\Services\Cart\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCounter extends Component
{
    #[On('cart-updated')]
    public function refresh(): void
    {
        // Re-render picks up the fresh cart count.
    }

    public function render()
    {
        return view('livewire.storefront.cart-counter', [
            'count' => app(CartService::class)->count(),
        ]);
    }
}
