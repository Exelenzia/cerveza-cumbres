<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id', 'document_series_id', 'tipo_comprobante', 'serie', 'correlativo', 'fecha_emision', 'moneda',
    'op_gravada', 'igv', 'total', 'estado', 'sunat_response_code', 'sunat_response_description', 'hash',
    'xml_path', 'cdr_path', 'pdf_path',
])]
class Invoice extends Model
{
    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_ACEPTADO = 'aceptado';

    public const ESTADO_RECHAZADO = 'rechazado';

    public const ESTADO_ERROR = 'error';

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'op_gravada' => 'decimal:2',
            'igv' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function documentSeries(): BelongsTo
    {
        return $this->belongsTo(DocumentSeries::class);
    }

    public function numeroCompleto(): string
    {
        return sprintf('%s-%s', $this->serie, str_pad((string) $this->correlativo, 8, '0', STR_PAD_LEFT));
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo_comprobante) {
            DocumentSeries::FACTURA => 'Factura',
            DocumentSeries::BOLETA => 'Boleta',
            default => $this->tipo_comprobante,
        };
    }
}
