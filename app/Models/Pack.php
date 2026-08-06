<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['name', 'slug', 'description', 'price', 'compare_at_price', 'is_active', 'sort_order'])]
class Pack extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'pack_items')
            ->withPivot('quantity')
            ->withTimestamps();
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
