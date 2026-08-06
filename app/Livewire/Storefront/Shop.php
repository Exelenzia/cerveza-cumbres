<?php

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\Pack;
use App\Models\PackTemplate;
use App\Models\Product;
use App\Services\Cart\CartService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Shop extends Component
{
    #[Url]
    public ?int $category = null;

    public function selectCategory(?int $categoryId): void
    {
        $this->category = $categoryId;
    }

    public function addToCart(string $type, int $id, ?int $variantId = null): void
    {
        app(CartService::class)->add($type, $id, variantId: $variantId);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->when($this->category, fn ($query) => $query->where('category_id', $this->category))
            ->with('variants')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.storefront.shop', [
            'categories' => Category::orderBy('sort_order')->get(),
            'products' => $products,
            'packs' => $this->category ? collect() : Pack::where('is_active', true)->orderBy('sort_order')->with('products')->get(),
            'packTemplates' => $this->category ? collect() : PackTemplate::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
