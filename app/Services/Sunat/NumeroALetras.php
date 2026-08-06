<?php

namespace App\Services\Sunat;

class NumeroALetras
{
    private const UNIDADES = [
        '', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ',
        'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE', 'VEINTE',
    ];

    private const DECENAS = [
        2 => 'VEINTI', 3 => 'TREINTA', 4 => 'CUARENTA', 5 => 'CINCUENTA',
        6 => 'SESENTA', 7 => 'SETENTA', 8 => 'OCHENTA', 9 => 'NOVENTA',
    ];

    private const CENTENAS = [
        1 => 'CIENTO', 2 => 'DOSCIENTOS', 3 => 'TRESCIENTOS', 4 => 'CUATROCIENTOS', 5 => 'QUINIENTOS',
        6 => 'SEISCIENTOS', 7 => 'SETECIENTOS', 8 => 'OCHOCIENTOS', 9 => 'NOVECIENTOS',
    ];

    public static function convertir(float $monto, string $moneda = 'SOLES'): string
    {
        $entero = (int) floor($monto);
        $centimos = (int) round(($monto - $entero) * 100);

        $letras = $entero === 0 ? 'CERO' : self::centenasCompletas($entero);

        return sprintf('SON %s CON %02d/100 %s', trim($letras), $centimos, $moneda);
    }

    private static function centenasCompletas(int $numero): string
    {
        if ($numero >= 1_000_000) {
            $millones = intdiv($numero, 1_000_000);
            $resto = $numero % 1_000_000;
            $prefijo = $millones === 1 ? 'UN MILLON' : self::centenasCompletas($millones).' MILLONES';

            return trim($prefijo.' '.($resto > 0 ? self::centenasCompletas($resto) : ''));
        }

        if ($numero >= 1000) {
            $miles = intdiv($numero, 1000);
            $resto = $numero % 1000;
            $prefijo = $miles === 1 ? 'MIL' : self::centenasCompletas($miles).' MIL';

            return trim($prefijo.' '.($resto > 0 ? self::centenasCompletas($resto) : ''));
        }

        if ($numero === 100) {
            return 'CIEN';
        }

        if ($numero >= 100) {
            return trim(self::CENTENAS[intdiv($numero, 100)].' '.self::decenasCompletas($numero % 100));
        }

        return self::decenasCompletas($numero);
    }

    private static function decenasCompletas(int $numero): string
    {
        if ($numero <= 20) {
            return self::UNIDADES[$numero];
        }

        if ($numero < 30) {
            $unidad = $numero - 20;

            return self::DECENAS[2].($unidad === 1 ? 'UNO' : self::UNIDADES[$unidad]);
        }

        $decena = intdiv($numero, 10);
        $unidad = $numero % 10;

        if ($unidad === 0) {
            return self::DECENAS[$decena];
        }

        $unidadTexto = $unidad === 1 ? 'UNO' : self::UNIDADES[$unidad];

        return self::DECENAS[$decena].' Y '.$unidadTexto;
    }
}
