<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function json(string $key, array $default = []): array
    {
        $value = static::get($key);

        if ($value === null) {
            return $default;
        }

        return json_decode($value, true) ?? $default;
    }

    public static function setJson(string $key, array $value): void
    {
        static::set($key, json_encode($value));
    }
}
