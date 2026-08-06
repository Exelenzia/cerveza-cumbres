<?php

namespace App\Services\Cart;

use App\Models\Product;

class StockReservationService
{
    /**
     * Atomically decrement stock for each product, failing fast (and rolling back
     * any decrements already applied in this call) if any product no longer has
     * enough stock. Each decrement is its own conditional UPDATE, so it's race-safe
     * without holding a transaction open across it.
     *
     * @param  array<int, int>  $requirements  product id => quantity required
     */
    public function reserve(array $requirements): ?int
    {
        $reserved = [];

        foreach ($requirements as $productId => $quantity) {
            $affected = Product::where('id', $productId)->where('stock', '>=', $quantity)->decrement('stock', $quantity);

            if ($affected === 0) {
                $this->release($reserved);

                return $productId;
            }

            $reserved[$productId] = $quantity;
        }

        return null;
    }

    /**
     * @param  array<int, int>  $requirements  product id => quantity to restore
     */
    public function release(array $requirements): void
    {
        foreach ($requirements as $productId => $quantity) {
            Product::where('id', $productId)->increment('stock', $quantity);
        }
    }
}
