<?php

namespace App\Livewire\Storefront;

use App\Models\PackTemplate;
use App\Services\Cart\CartService;
use App\Services\Packs\PackPricingService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class PackBuilder extends Component
{
    public PackTemplate $template;

    /** @var array<int, int> product id => quantity */
    public array $selections = [];

    public ?string $error = null;

    public function mount(PackTemplate $template): void
    {
        abort_unless($template->is_active, 404);

        $this->template = $template;
    }

    public function increment(int $productId): void
    {
        $this->error = null;

        if ($this->template->type === PackTemplate::TYPE_FIXED_STYLE) {
            $this->selections = [$productId => $this->template->bottle_count];

            return;
        }

        $totalUnits = array_sum($this->selections);

        if ($totalUnits >= $this->template->bottle_count) {
            return;
        }

        $this->selections[$productId] = ($this->selections[$productId] ?? 0) + 1;
    }

    public function decrement(int $productId): void
    {
        $this->error = null;

        if (! isset($this->selections[$productId])) {
            return;
        }

        $this->selections[$productId]--;

        if ($this->selections[$productId] <= 0) {
            unset($this->selections[$productId]);
        }
    }

    public function addToCart(PackPricingService $pricing): void
    {
        $error = $pricing->validateSelections($this->template, $this->selections);

        if ($error) {
            $this->error = $error;

            return;
        }

        app(CartService::class)->addCustomPack($this->template->id, $this->selections);
        $this->dispatch('cart-updated');
        $this->redirectRoute('cart');
    }

    public function render(PackPricingService $pricing)
    {
        $eligibleProducts = $this->template->eligibleProducts()->where('is_active', true)->orderBy('name')->get();

        return view('livewire.storefront.pack-builder', [
            'title' => $this->template->name,
            'eligibleProducts' => $eligibleProducts,
            'totalUnits' => array_sum($this->selections),
            'price' => $pricing->price($this->template, $this->selections),
            'includedMerch' => $this->template->includedMerchProduct,
        ]);
    }
}
