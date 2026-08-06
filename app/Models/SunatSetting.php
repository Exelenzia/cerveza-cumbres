<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ruc', 'razon_social', 'nombre_comercial', 'ubigeo', 'departamento', 'provincia', 'distrito',
    'direccion', 'sol_usuario', 'sol_clave', 'certificado_path', 'certificado_password', 'modo', 'is_active',
])]
class SunatSetting extends Model
{
    public const MODO_BETA = 'beta';

    public const MODO_PRODUCCION = 'produccion';

    protected function casts(): array
    {
        return [
            'sol_clave' => 'encrypted',
            'certificado_password' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public static function current(): ?self
    {
        return static::query()->where('is_active', true)->first();
    }

    public function isBeta(): bool
    {
        return $this->modo === self::MODO_BETA;
    }
}
