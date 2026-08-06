<div id="culqi-checkout-root" data-amount="{{ (int) round($total * 100) }}" class="mx-auto max-w-5xl px-6 py-16">
    <h1 class="mb-10 font-display text-4xl uppercase tracking-wide text-text">Checkout</h1>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form wire:submit="proceedToPayment" class="space-y-4 rounded-xl border border-border bg-surface-muted p-6">
                <h2 class="font-display text-lg uppercase tracking-wide text-text">Datos de contacto y envío</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm text-text">Nombre completo</label>
                        <input type="text" wire:model="customer_name" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('customer_name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text">Correo</label>
                        <input type="email" wire:model="customer_email" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('customer_email') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text">Teléfono</label>
                        <input type="text" wire:model="customer_phone" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text">Ciudad</label>
                        <input type="text" wire:model="shipping_city" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('shipping_city') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text">Comprobante</label>
                        <select wire:model.live="customer_document_type" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            <option value="dni">Boleta (DNI)</option>
                            <option value="ruc">Factura (RUC)</option>
                        </select>
                        @error('customer_document_type') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text">{{ $customer_document_type === 'ruc' ? 'RUC' : 'DNI' }}</label>
                        <input type="text" wire:model="customer_document_number" maxlength="11" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('customer_document_number') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if ($shippingZones->isNotEmpty())
                    <div>
                        <label class="mb-1 block text-sm text-text">Zona de envío</label>
                        <select wire:model.live="shipping_zone_id" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            @foreach ($shippingZones as $zone)
                                <option value="{{ $zone->id }}">
                                    {{ $zone->name }} — S/ {{ number_format($zone->price, 2) }}@if ($zone->eta_label) ({{ $zone->eta_label }})@endif
                                </option>
                            @endforeach
                        </select>
                        @error('shipping_zone_id') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div>
                    <label class="mb-1 block text-sm text-text">Dirección de entrega</label>
                    <input type="text" wire:model="shipping_address" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    @error('shipping_address') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm text-text">Notas (opcional)</label>
                    <textarea wire:model="notes" rows="2" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"></textarea>
                </div>

                @if ($paymentError)
                    <div class="rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                        {{ $paymentError }}
                    </div>
                @endif

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="proceedToPayment,pay"
                    class="w-full rounded-lg bg-primary px-6 py-3 font-semibold text-primary-on hover:bg-primary-hover disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="proceedToPayment,pay">Pagar con tarjeta · S/ {{ number_format($total, 2) }}</span>
                    <span wire:loading wire:target="proceedToPayment,pay">Procesando…</span>
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-border bg-surface-muted p-6">
            <h2 class="mb-4 font-display text-lg uppercase tracking-wide text-text">Resumen</h2>
            <div class="space-y-3">
                @foreach ($items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-text/80">{{ $item['model']->name }} × {{ $item['quantity'] }}</span>
                        <span class="text-text">S/ {{ number_format($item['lineTotal'], 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 space-y-2 border-t border-border pt-4 text-sm">
                <div class="flex justify-between text-text/80">
                    <span>Subtotal</span>
                    <span>S/ {{ number_format($subtotal, 2) }}</span>
                </div>
                @if ($coupon)
                    <div class="flex justify-between text-primary">
                        <span>Cupón {{ $coupon->code }}</span>
                        <span>- S/ {{ number_format($discount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-text/80">
                    <span>Envío</span>
                    <span>{{ $shippingZones->isNotEmpty() ? 'S/ '.number_format($shippingCost, 2) : 'Por confirmar' }}</span>
                </div>
                <div class="flex justify-between font-display text-lg text-primary">
                    <span>Total</span>
                    <span>S/ {{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    @if ($culqiPublicKey)
        <script src="https://checkout.culqi.com/js/v4"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Culqi.publicKey = @json($culqiPublicKey);

                Culqi.options({
                    lang: 'auto',
                    installments: false,
                    paymentMethods: {
                        tarjeta: true,
                        yape: false,
                        bancaMovil: false,
                        agente: false,
                        billetera: false,
                        cuotealo: false,
                    },
                });

                window.culqi = function () {
                    if (Culqi.token) {
                        $wire.pay(Culqi.token.id);
                    } else if (Culqi.error) {
                        $wire.setPaymentError(Culqi.error.user_message || 'No se pudo procesar el pago.');
                    }
                };

                $wire.on('open-culqi', () => {
                    const amount = parseInt(document.getElementById('culqi-checkout-root').dataset.amount, 10);

                    Culqi.settings({
                        title: 'Cumbres',
                        currency: 'PEN',
                        amount: amount,
                    });

                    Culqi.open();
                });
            });
        </script>
    @else
        <p class="mt-6 text-center text-sm text-red-400">
            Culqi no está configurado todavía (faltan las llaves en el archivo .env).
        </p>
    @endif
</div>
