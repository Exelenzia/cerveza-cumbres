<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'phone_number_id', 'access_token', 'business_account_id', 'wa_link_phone', 'template_language',
    'template_confirmacion', 'template_enviado', 'template_entregado', 'is_active',
])]
class WhatsappSetting extends Model
{
    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public static function current(): ?self
    {
        return static::query()->where('is_active', true)->first();
    }
}
