<?php

namespace App\Services\Cart;

use Illuminate\Database\Eloquent\Model;

class StockReservationService
{
    /**
     * Atomically decrement stock for each record, failing fast (and rolling back
     * any decrements already applied in this call) if any record no longer has
     * enough stock. Each decrement is its own conditional UPDATE, so it's race-safe
     * without holding a transaction open across it.
     *
     * @param  class-string<Model>  $modelClass  Product::class or ProductVariant::class — both expose a `stock` column
     * @param  array<int, int>  $requirements  record id => quantity required
     */
    public function reserve(string $modelClass, array $requirements): ?int
    {
        $reserved = [];

        foreach ($requirements as $id => $quantity) {
            $affected = $modelClass::where('id', $id)->where('stock', '>=', $quantity)->decrement('stock', $quantity);

            if ($affected === 0) {
                $this->release($modelClass, $reserved);

                return $id;
            }

            $reserved[$id] = $quantity;
        }

        return null;
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<int, int>  $requirements  record id => quantity to restore
     */
    public function release(string $modelClass, array $requirements): void
    {
        foreach ($requirements as $id => $quantity) {
            $modelClass::where('id', $id)->increment('stock', $quantity);
        }
    }
}
