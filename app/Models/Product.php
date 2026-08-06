<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'category_id', 'name', 'slug', 'style', 'description',
    'abv', 'ibu', 'volume_ml', 'price', 'compare_at_price',
    'stock', 'is_popular', 'is_active', 'sort_order',
    'fixed_pack6_price', 'is_mix_premium', 'mix_surcharge_per_unit',
])]
class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'abv' => 'decimal:1',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'fixed_pack6_price' => 'decimal:2',
            'is_mix_premium' => 'boolean',
            'mix_surcharge_per_unit' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function getHasVariantsAttribute(): bool
    {
        return $this->variants->isNotEmpty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useFallbackUrl('/images/product-placeholder.svg');
    }

    public function getCoverUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('images');
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->compare_at_price || $this->compare_at_price <= $this->price) {
            return null;
        }

        return (int) round((1 - ($this->price / $this->compare_at_price)) * 100);
    }
}
