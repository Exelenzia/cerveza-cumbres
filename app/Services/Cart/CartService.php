<?php

namespace App\Services\Cart;

use App\Models\Pack;
use App\Models\PackTemplate;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Packs\PackPricingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    private const SESSION_KEY = 'cart';

    private const COUPON_SESSION_KEY = 'cart_coupon_code';

    public function __construct(private readonly PackPricingService $pricing) {}

    public function add(string $type, int $id, int $quantity = 1, ?int $variantId = null): void
    {
        $key = $variantId ? "{$type}-{$id}-v{$variantId}" : "{$type}-{$id}";
        $cart = $this->raw();

        $cart[$key] = [
            'type' => $type,
            'id' => $id,
            'variant_id' => $variantId,
            'quantity' => max(1, ($cart[$key]['quantity'] ?? 0) + $quantity),
        ];

        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * @param  array<int, int>  $selections  product id => quantity
     */
    public function addCustomPack(int $templateId, array $selections, int $quantity = 1): string
    {
        $key = 'custom_pack-'.(string) Str::uuid();
        $cart = $this->raw();

        $cart[$key] = [
            'type' => 'custom_pack',
            'template_id' => $templateId,
            'selections' => $selections,
            'quantity' => max(1, $quantity),
        ];

        Session::put(self::SESSION_KEY, $cart);

        return $key;
    }

    public function update(string $key, int $quantity): void
    {
        $cart = $this->raw();

        if (! isset($cart[$key])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = $quantity;
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function remove(string $key): void
    {
        $cart = $this->raw();
        unset($cart[$key]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
        $this->clearCoupon();
    }

    public function couponCode(): ?string
    {
        return Session::get(self::COUPON_SESSION_KEY);
    }

    public function setCouponCode(string $code): void
    {
        Session::put(self::COUPON_SESSION_KEY, $code);
    }

    public function clearCoupon(): void
    {
        Session::forget(self::COUPON_SESSION_KEY);
    }

    public function isEmpty(): bool
    {
        return $this->items()->isEmpty();
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('lineTotal');
    }

    /**
     * @return Collection<int, array{key: string, type: string, model: Product|Pack|PackTemplate, variant: ?ProductVariant, quantity: int, unitPrice: float, lineTotal: float, availableStock: int}>
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        $productEntries = collect($cart)->where('type', 'product');
        $packIds = collect($cart)->where('type', 'pack')->pluck('id');
        $customPackEntries = collect($cart)->where('type', 'custom_pack');

        $variantIds = $productEntries->pluck('variant_id')->filter()->values();

        $templateIds = $customPackEntries->pluck('template_id')->unique();
        $templates = PackTemplate::whereIn('id', $templateIds)->where('is_active', true)->get()->keyBy('id');

        $customPackProductIds = collect();
        foreach ($customPackEntries as $entry) {
            $customPackProductIds = $customPackProductIds->merge(array_keys($entry['selections'] ?? []));
        }

        $includedMerchIds = $templates->pluck('included_merch_product_id')->filter();

        $allProductIds = $productEntries->pluck('id')
            ->merge($customPackProductIds)
            ->merge($includedMerchIds)
            ->unique();

        $products = Product::whereIn('id', $allProductIds)->where('is_active', true)->get()->keyBy('id');
        $variants = ProductVariant::whereIn('id', $variantIds)->where('is_active', true)->get()->keyBy('id');
        $packs = Pack::whereIn('id', $packIds)->where('is_active', true)->with('products')->get()->keyBy('id');

        return collect($cart)
            ->map(fn (array $entry, string $key) => match ($entry['type']) {
                'product' => $this->hydrateProductLine($key, $entry, $products, $variants),
                'pack' => $this->hydratePackLine($key, $entry, $packs),
                'custom_pack' => $this->hydrateCustomPackLine($key, $entry, $templates, $products),
                default => null,
            })
            ->filter()
            ->values();
    }

    /**
     * Net product stock requirements across all cart lines (packs and custom packs expanded
     * into their constituent products), so the same product isn't decremented twice under
     * two different guises. Variant-priced product lines are excluded here — see variantRequirements().
     *
     * @param  Collection  $items  result of items()
     * @return array<int, int> product id => quantity required
     */
    public function productRequirements(Collection $items): array
    {
        $requirements = [];

        foreach ($items as $item) {
            if ($item['type'] === 'product') {
                if ($item['variant']) {
                    continue;
                }

                $requirements[$item['model']->id] = ($requirements[$item['model']->id] ?? 0) + $item['quantity'];

                continue;
            }

            if ($item['type'] === 'pack') {
                foreach ($item['model']->products as $product) {
                    $needed = $product->pivot->quantity * $item['quantity'];
                    $requirements[$product->id] = ($requirements[$product->id] ?? 0) + $needed;
                }

                continue;
            }

            if ($item['type'] === 'custom_pack') {
                foreach ($item['selections'] as $productId => $qty) {
                    $needed = $qty * $item['quantity'];
                    $requirements[$productId] = ($requirements[$productId] ?? 0) + $needed;
                }

                if ($merchId = $item['template']->included_merch_product_id) {
                    $requirements[$merchId] = ($requirements[$merchId] ?? 0) + $item['quantity'];
                }
            }
        }

        return $requirements;
    }

    /**
     * @param  Collection  $items  result of items()
     * @return array<int, int> variant id => quantity required
     */
    public function variantRequirements(Collection $items): array
    {
        $requirements = [];

        foreach ($items as $item) {
            if ($item['type'] === 'product' && $item['variant']) {
                $requirements[$item['variant']->id] = ($requirements[$item['variant']->id] ?? 0) + $item['quantity'];
            }
        }

        return $requirements;
    }

    private function hydrateProductLine(string $key, array $entry, Collection $products, Collection $variants): ?array
    {
        $model = $products->get($entry['id']);

        if (! $model) {
            return null;
        }

        $variantId = $entry['variant_id'] ?? null;
        $variant = $variantId ? $variants->get($variantId) : null;

        if ($variantId && ! $variant) {
            return null;
        }

        $unitPrice = $variant ? $variant->price : (float) $model->price;
        $quantity = $entry['quantity'];

        return [
            'key' => $key,
            'type' => 'product',
            'model' => $model,
            'variant' => $variant,
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'lineTotal' => round($unitPrice * $quantity, 2),
            'availableStock' => $variant ? $variant->stock : $model->stock,
        ];
    }

    private function hydratePackLine(string $key, array $entry, Collection $packs): ?array
    {
        $model = $packs->get($entry['id']);

        if (! $model) {
            return null;
        }

        $unitPrice = (float) $model->price;
        $quantity = $entry['quantity'];

        return [
            'key' => $key,
            'type' => 'pack',
            'model' => $model,
            'variant' => null,
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'lineTotal' => round($unitPrice * $quantity, 2),
            'availableStock' => $this->packAvailableStock($model),
        ];
    }

    private function hydrateCustomPackLine(string $key, array $entry, Collection $templates, Collection $products): ?array
    {
        $template = $templates->get($entry['template_id']);

        if (! $template) {
            return null;
        }

        $selections = array_map('intval', $entry['selections'] ?? []);
        $quantity = $entry['quantity'];

        $composition = collect($selections)
            ->map(fn (int $qty, int $productId) => [
                'product_id' => $productId,
                'name' => $products->get($productId)?->name ?? 'Producto',
                'quantity' => $qty,
            ])
            ->values();

        $unitPrice = $this->pricing->price($template, $selections);

        $stockMap = $selections;

        if ($template->included_merch_product_id) {
            $stockMap[$template->included_merch_product_id] = ($stockMap[$template->included_merch_product_id] ?? 0) + 1;
        }

        return [
            'key' => $key,
            'type' => 'custom_pack',
            'model' => $template,
            'template' => $template,
            'variant' => null,
            'selections' => $selections,
            'composition' => $composition,
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'lineTotal' => round($unitPrice * $quantity, 2),
            'availableStock' => $this->minAvailableCopies($stockMap, $products),
        ];
    }

    private function packAvailableStock(Pack $pack): int
    {
        if ($pack->products->isEmpty()) {
            return 0;
        }

        return (int) $pack->products->map(fn (Product $product) => intdiv($product->stock, max(1, $product->pivot->quantity)))->min();
    }

    /**
     * @param  array<int, int>  $productQtyMap  product id => quantity needed per copy
     */
    private function minAvailableCopies(array $productQtyMap, Collection $products): int
    {
        if (empty($productQtyMap)) {
            return 0;
        }

        $min = null;

        foreach ($productQtyMap as $productId => $qtyPerCopy) {
            $product = $products->get((int) $productId);
            $available = $product ? intdiv($product->stock, max(1, $qtyPerCopy)) : 0;
            $min = $min === null ? $available : min($min, $available);
        }

        return (int) $min;
    }

    private function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}
