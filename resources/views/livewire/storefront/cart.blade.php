<div class="mx-auto max-w-4xl px-6 py-16">
    <h1 class="mb-10 font-display text-4xl uppercase tracking-wide text-cream-50">Tu carrito</h1>

    @if ($items->isEmpty())
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-10 text-center">
            <p class="text-cream-200/70">Tu carrito está vacío.</p>
            <a href="{{ route('shop') }}" wire:navigate class="mt-6 inline-block rounded-lg bg-gold-500 px-6 py-3 font-semibold text-cumbre-950 hover:bg-gold-400">
                Ir a la tienda
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($items as $item)
                <div class="flex items-center gap-4 rounded-xl border border-cumbre-700 bg-cumbre-900 p-4">
                    <img src="{{ $item['model']->cover_url }}" alt="{{ $item['model']->name }}" class="h-16 w-16 rounded-lg object-cover">
                    <div class="flex-1">
                        <p class="font-display text-lg text-cream-50">{{ $item['model']->name }}</p>
                        <p class="text-sm text-cream-200/60">S/ {{ number_format($item['unitPrice'], 2) }} c/u</p>
                        @if ($item['quantity'] > $item['availableStock'])
                            <p class="mt-1 text-sm text-red-400">
                                @if ($item['availableStock'] <= 0)
                                    Sin stock disponible.
                                @else
                                    Solo quedan {{ $item['availableStock'] }} disponibles.
                                @endif
                            </p>
                        @endif
                    </div>
                    <input
                        type="number"
                        min="1"
                        value="{{ $item['quantity'] }}"
                        wire:change="updateQuantity('{{ $item['key'] }}', $event.target.value)"
                        class="w-20 rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-center text-cream-50 focus:border-gold-500 focus:outline-none"
                    >
                    <p class="w-24 text-right font-semibold text-cream-100">S/ {{ number_format($item['lineTotal'], 2) }}</p>
                    <button wire:click="remove('{{ $item['key'] }}')" class="text-red-400 hover:text-red-300">Quitar</button>
                </div>
            @endforeach
        </div>

        <div class="mt-8 rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <p class="mb-2 text-sm text-cream-200/70">¿Tienes un cupón?</p>
            <div class="flex gap-3">
                <input
                    type="text"
                    wire:model="couponInput"
                    placeholder="Código de cupón"
                    class="flex-1 rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none"
                >
                <button wire:click="applyCoupon" class="rounded-lg border border-gold-500 px-5 py-2 text-sm font-semibold text-gold-400 hover:bg-gold-500 hover:text-cumbre-950">
                    Aplicar
                </button>
            </div>
            @if ($couponError)
                <p class="mt-2 text-sm text-red-400">{{ $couponError }}</p>
            @endif
            @if ($coupon)
                <div class="mt-3 flex items-center justify-between rounded-lg bg-cumbre-950/60 px-3 py-2">
                    <span class="text-sm text-cream-100">Cupón <strong class="text-gold-400">{{ $coupon->code }}</strong> aplicado</span>
                    <button wire:click="removeCoupon" class="text-sm text-red-400 hover:text-red-300">Quitar</button>
                </div>
            @endif
        </div>

        <div class="mt-6 rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-cream-200/80">
                    <span>Subtotal</span>
                    <span>S/ {{ number_format($subtotal, 2) }}</span>
                </div>
                @if ($discount > 0)
                    <div class="flex justify-between text-gold-400">
                        <span>Descuento</span>
                        <span>- S/ {{ number_format($discount, 2) }}</span>
                    </div>
                @endif
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-cumbre-700 pt-4">
                <p class="font-display text-2xl text-gold-400">S/ {{ number_format($total, 2) }}</p>
                <a href="{{ route('checkout') }}" wire:navigate class="rounded-lg bg-gold-500 px-8 py-3 font-semibold text-cumbre-950 hover:bg-gold-400">
                    Proceder al pago
                </a>
            </div>
        </div>
    @endif
</div>
