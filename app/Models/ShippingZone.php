<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['name', 'cities', 'price', 'eta_label', 'is_active', 'sort_order'])]
class ShippingZone extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function cityList(): array
    {
        return collect(explode(',', $this->cities))
            ->map(fn (string $city) => trim($city))
            ->filter()
            ->values()
            ->all();
    }

    public static function findForCity(string $city): ?self
    {
        $needle = Str::lower(trim($city));

        if ($needle === '') {
            return null;
        }

        return static::query()
            ->where('is_active', true)
            ->get()
            ->first(fn (self $zone) => collect($zone->cityList())->contains(
                fn (string $c) => Str::lower($c) === $needle
            ));
    }
}
