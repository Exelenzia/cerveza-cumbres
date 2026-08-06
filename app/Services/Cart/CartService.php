<?php

namespace App\Services\Cart;

use App\Models\Pack;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    private const COUPON_SESSION_KEY = 'cart_coupon_code';

    public function add(string $type, int $id, int $quantity = 1): void
    {
        $key = "{$type}-{$id}";
        $cart = $this->raw();

        $cart[$key] = [
            'type' => $type,
            'id' => $id,
            'quantity' => max(1, ($cart[$key]['quantity'] ?? 0) + $quantity),
        ];

        Session::put(self::SESSION_KEY, $cart);
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
     * @return Collection<int, array{key: string, type: string, model: Product|Pack, quantity: int, unitPrice: float, lineTotal: float}>
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        $productIds = collect($cart)->where('type', 'product')->pluck('id');
        $packIds = collect($cart)->where('type', 'pack')->pluck('id');

        $products = Product::whereIn('id', $productIds)->where('is_active', true)->get()->keyBy('id');
        $packs = Pack::whereIn('id', $packIds)->where('is_active', true)->get()->keyBy('id');

        return collect($cart)
            ->map(function (array $entry, string $key) use ($products, $packs) {
                $model = $entry['type'] === 'product'
                    ? $products->get($entry['id'])
                    : $packs->get($entry['id']);

                if (! $model) {
                    return null;
                }

                $unitPrice = (float) $model->price;
                $quantity = $entry['quantity'];

                return [
                    'key' => $key,
                    'type' => $entry['type'],
                    'model' => $model,
                    'quantity' => $quantity,
                    'unitPrice' => $unitPrice,
                    'lineTotal' => round($unitPrice * $quantity, 2),
                ];
            })
            ->filter()
            ->values();
    }

    private function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}
