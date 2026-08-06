<?php

namespace App\Services\Culqi;

use Illuminate\Support\Facades\Http;

class CulqiService
{
    private const API_URL = 'https://api.culqi.com/v2/charges';

    /**
     * Charge a Culqi.js token. Amount is in the currency's base unit (soles),
     * converted internally to céntimos as Culqi's API requires.
     *
     * @return string The Culqi charge id, used as the payment reference.
     *
     * @throws CulqiChargeException
     */
    public function charge(string $token, float $amount, string $email, string $currency = 'PEN'): string
    {
        $secretKey = config('services.culqi.secret_key');

        if (! $secretKey) {
            throw new CulqiChargeException('Culqi no está configurado (falta CULQI_SECRET_KEY).');
        }

        $response = Http::withToken($secretKey)
            ->post(self::API_URL, [
                'amount' => (int) round($amount * 100),
                'currency_code' => $currency,
                'email' => $email,
                'source_id' => $token,
            ]);

        if ($response->failed()) {
            $message = $response->json('user_message') ?? $response->json('merchant_message') ?? 'No se pudo procesar el pago.';

            throw new CulqiChargeException($message);
        }

        return (string) $response->json('id');
    }
}
