<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name', 'slug', 'type', 'bottle_count', 'base_price',
    'included_merch_product_id', 'delivery_cost', 'free_shipping_eligible',
    'delivery_note', 'description', 'is_active', 'sort_order',
])]
class PackTemplate extends Model
{
    public const TYPE_FIXED_STYLE = 'fixed_style';

    public const TYPE_MIX = 'mix';

    public const TYPE_GIFT = 'gift';

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'delivery_cost' => 'decimal:2',
            'free_shipping_eligible' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function eligibleProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'pack_template_eligible_products');
    }

    public function includedMerchProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'included_merch_product_id');
    }
}
