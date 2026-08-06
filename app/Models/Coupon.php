<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

#[Fillable([
    'code', 'type', 'value', 'scope', 'product_id', 'min_order_amount',
    'max_uses', 'used_count', 'expires_at', 'is_active',
])]
class Coupon extends Model
{
    public const TYPE_FIXED = 'fixed';

    public const TYPE_PERCENTAGE = 'percentage';

    public const SCOPE_ORDER = 'order';

    public const SCOPE_PRODUCT = 'product';

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return string|null Error message key, or null if the coupon can be used against this cart.
     */
    public function validationError(float $subtotal, Collection $items): ?string
    {
        if (! $this->is_active) {
            return 'Este cupón ya no está disponible.';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Este cupón ha expirado.';
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'Este cupón alcanzó su límite de usos.';
        }

        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return 'El monto mínimo para este cupón es S/ '.number_format((float) $this->min_order_amount, 2).'.';
        }

        if ($this->scope === self::SCOPE_PRODUCT) {
            $hasProduct = $items->contains(fn (array $item) => $item['type'] === 'product' && $item['model']->id === $this->product_id);

            if (! $hasProduct) {
                return 'Este cupón solo aplica a un producto que no está en tu carrito.';
            }
        }

        return null;
    }

    public function calculateDiscount(float $subtotal, Collection $items): float
    {
        $base = $subtotal;

        if ($this->scope === self::SCOPE_PRODUCT) {
            $base = (float) $items
                ->filter(fn (array $item) => $item['type'] === 'product' && $item['model']->id === $this->product_id)
                ->sum('lineTotal');
        }

        $discount = $this->type === self::TYPE_PERCENTAGE
            ? $base * ((float) $this->value / 100)
            : (float) $this->value;

        return round(min($discount, $base), 2);
    }
}
