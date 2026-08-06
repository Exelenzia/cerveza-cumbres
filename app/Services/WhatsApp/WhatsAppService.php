<?php

namespace App\Services\WhatsApp;

use App\Models\Order;
use App\Models\WhatsappSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private const API_VERSION = 'v20.0';

    /**
     * Send an approved WhatsApp template message via the Meta Cloud API.
     *
     * @param  string[]  $bodyParams  Values for the template body's numbered placeholders, in order.
     *
     * @throws WhatsAppException
     */
    public function sendTemplate(string $to, string $templateName, string $languageCode, array $bodyParams = []): void
    {
        $settings = WhatsappSetting::current();

        if (! $settings) {
            throw new WhatsAppException('WhatsApp no está configurado.');
        }

        $components = [];

        if ($bodyParams !== []) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(fn (string $value) => ['type' => 'text', 'text' => $value], $bodyParams),
            ];
        }

        $response = Http::withToken($settings->access_token)
            ->post(sprintf('https://graph.facebook.com/%s/%s/messages', self::API_VERSION, $settings->phone_number_id), [
                'messaging_product' => 'whatsapp',
                'to' => $this->normalizePhone($to),
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => $languageCode],
                    'components' => $components,
                ],
            ]);

        if ($response->failed()) {
            $message = $response->json('error.message') ?? 'No se pudo enviar el mensaje de WhatsApp.';

            throw new WhatsAppException($message);
        }
    }

    public function notifyOrderConfirmed(Order $order): bool
    {
        return $this->notify($order, fn (WhatsappSetting $s) => $s->template_confirmacion, [
            $order->customer_name,
            $order->order_number,
            'S/ '.number_format((float) $order->total, 2),
        ]);
    }

    public function notifyOrderShipped(Order $order): bool
    {
        return $this->notify($order, fn (WhatsappSetting $s) => $s->template_enviado, [
            $order->customer_name,
            $order->order_number,
        ]);
    }

    public function notifyOrderDelivered(Order $order): bool
    {
        return $this->notify($order, fn (WhatsappSetting $s) => $s->template_entregado, [
            $order->customer_name,
            $order->order_number,
        ]);
    }

    /**
     * @param  callable(WhatsappSetting): ?string  $templateResolver
     * @param  string[]  $bodyParams
     */
    private function notify(Order $order, callable $templateResolver, array $bodyParams): bool
    {
        $settings = WhatsappSetting::current();
        $templateName = $settings ? $templateResolver($settings) : null;

        if (! $settings || ! $templateName || ! $order->customer_phone) {
            return false;
        }

        try {
            $this->sendTemplate($order->customer_phone, $templateName, $settings->template_language, $bodyParams);

            return true;
        } catch (WhatsAppException $e) {
            Log::warning('No se pudo enviar la notificación de WhatsApp.', [
                'order_id' => $order->id,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) === 9 && $digits[0] === '9') {
            return '51'.$digits;
        }

        return $digits;
    }
}
