<?php

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\PageBlock;
use App\Models\Product;
use App\Services\Cart\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Home extends Component
{
    public function addToCart(string $type, int $id, ?int $variantId = null): void
    {
        app(CartService::class)->add($type, $id, variantId: $variantId);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.storefront.home', [
            'categories' => Category::orderBy('sort_order')->withCount('products')->get(),
            'popularProducts' => Product::where('is_active', true)->where('is_popular', true)->with('variants')->orderBy('sort_order')->take(4)->get(),
            'teamBlocks' => PageBlock::where('type', PageBlock::TYPE_TEAM)->where('show_on_homepage', true)->orderBy('sort_order')->get(),
        ]);
    }
}
