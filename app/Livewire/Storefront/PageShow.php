<?php

namespace App\Livewire\Storefront;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Product;
use App\Services\Cart\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class PageShow extends Component
{
    public Page $page;

    public function mount(Page $page): void
    {
        abort_unless($page->is_active, 404);

        $this->page = $page;
    }

    public function addToCart(string $type, int $id): void
    {
        app(CartService::class)->add($type, $id);
        $this->dispatch('cart-updated');
    }

    private function productsForBlock(array $data)
    {
        $limit = (int) ($data['limit'] ?? 4);

        if (($data['source'] ?? 'popular') === 'category' && ! empty($data['category_id'])) {
            return Product::where('is_active', true)
                ->where('category_id', $data['category_id'])
                ->orderBy('sort_order')
                ->take($limit)
                ->get();
        }

        return Product::where('is_active', true)
            ->where('is_popular', true)
            ->orderBy('sort_order')
            ->take($limit)
            ->get();
    }

    public function render()
    {
        $blocks = $this->page->blocks->map(fn (PageBlock $block) => [
            'block' => $block,
            'products' => $block->type === PageBlock::TYPE_PRODUCT_GRID
                ? $this->productsForBlock($block->data)
                : null,
        ]);

        return view('livewire.storefront.page-show', [
            'title' => $this->page->title,
            'blocks' => $blocks,
        ]);
    }
}
