<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['page_id', 'type', 'data', 'sort_order'])]
class PageBlock extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const TYPE_HERO = 'hero';

    public const TYPE_TEXT_IMAGE = 'text_image';

    public const TYPE_PRODUCT_GRID = 'product_grid';

    public const TYPE_TESTIMONIALS = 'testimonials';

    public const TYPE_CTA = 'cta';

    public const TYPES = [
        self::TYPE_HERO,
        self::TYPE_TEXT_IMAGE,
        self::TYPE_PRODUCT_GRID,
        self::TYPE_TESTIMONIALS,
        self::TYPE_CTA,
    ];

    public const LABELS = [
        self::TYPE_HERO => 'Hero',
        self::TYPE_TEXT_IMAGE => 'Texto + imagen',
        self::TYPE_PRODUCT_GRID => 'Grid de productos',
        self::TYPE_TESTIMONIALS => 'Testimonios',
        self::TYPE_CTA => 'Llamado a la acción',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public static function defaultData(string $type): array
    {
        return match ($type) {
            self::TYPE_HERO => ['heading' => '', 'subheading' => '', 'button_label' => '', 'button_link' => ''],
            self::TYPE_TEXT_IMAGE => ['heading' => '', 'body' => '', 'image_position' => 'left'],
            self::TYPE_PRODUCT_GRID => ['heading' => '', 'source' => 'popular', 'category_id' => null, 'limit' => 4],
            self::TYPE_TESTIMONIALS => ['heading' => '', 'items' => [['author' => '', 'quote' => '']]],
            self::TYPE_CTA => ['heading' => '', 'body' => '', 'button_label' => '', 'button_link' => ''],
            default => [],
        };
    }
}
