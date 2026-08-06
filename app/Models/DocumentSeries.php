<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable(['tipo_comprobante', 'serie', 'correlativo', 'is_active'])]
class DocumentSeries extends Model
{
    public const FACTURA = '01';

    public const BOLETA = '03';

    protected function casts(): array
    {
        return [
            'correlativo' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function nextCorrelativo(): int
    {
        return DB::transaction(function (): int {
            $series = static::query()->whereKey($this->id)->lockForUpdate()->first();

            $next = $series->correlativo + 1;

            $series->update(['correlativo' => $next]);

            $this->correlativo = $next;

            return $next;
        });
    }
}
