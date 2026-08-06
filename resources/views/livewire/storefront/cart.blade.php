<div class="mx-auto max-w-4xl px-6 py-16">
    <h1 class="mb-10 font-display text-4xl uppercase tracking-wide text-text">Tu carrito</h1>

    @if ($items->isEmpty())
        <div class="rounded-xl border border-border bg-surface-muted p-10 text-center">
            <p class="text-text/70">Tu carrito está vacío.</p>
            <a href="{{ route('shop') }}" wire:navigate class="mt-6 inline-block rounded-lg bg-primary px-6 py-3 font-semibold text-primary-on hover:bg-primary-hover">
                Ir a la tienda
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($items as $item)
                <div class="flex items-center gap-4 rounded-xl border border-border bg-surface-muted p-4">
                    @if ($item['type'] === 'custom_pack')
                        <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-surface text-xs font-semibold uppercase text-secondary">Pack</div>
                    @else
                        <img src="{{ $item['model']->cover_url }}" alt="{{ $item['model']->name }}" class="h-16 w-16 rounded-lg object-cover">
                    @endif
                    <div class="flex-1">
                        <p class="font-display text-lg text-text">{{ $item['model']->name }}</p>
                        @if ($item['type'] === 'product' && $item['variant'])
                            <p class="text-sm text-text/60">Variante: {{ $item['variant']->label }}</p>
                        @endif
                        @if ($item['type'] === 'custom_pack')
                            <p class="text-sm text-text/60">
                                {{ $item['composition']->map(fn ($line) => "{$line['quantity']}× {$line['name']}")->implode(', ') }}
                            </p>
                        @endif
                        <p class="text-sm text-text/60">S/ {{ number_format($item['unitPrice'], 2) }} c/u</p>
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
                        class="w-20 rounded-lg border border-border bg-surface px-3 py-2 text-center text-text focus:border-primary focus:outline-none"
                    >
                    <p class="w-24 text-right font-semibold text-text">S/ {{ number_format($item['lineTotal'], 2) }}</p>
                    <button wire:click="remove('{{ $item['key'] }}')" class="text-red-400 hover:text-red-300">Quitar</button>
                </div>
            @endforeach
        </div>

        <div class="mt-8 rounded-xl border border-border bg-surface-muted p-6">
            <p class="mb-2 text-sm text-text/70">¿Tienes un cupón?</p>
            <div class="flex gap-3">
                <input
                    type="text"
                    wire:model="couponInput"
                    placeholder="Código de cupón"
                    class="flex-1 rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"
                >
                <button wire:click="applyCoupon" class="rounded-lg border border-primary px-5 py-2 text-sm font-semibold text-primary hover:bg-primary hover:text-primary-on">
                    Aplicar
                </button>
            </div>
            @if ($couponError)
                <p class="mt-2 text-sm text-red-400">{{ $couponError }}</p>
            @endif
            @if ($coupon)
                <div class="mt-3 flex items-center justify-between rounded-lg bg-surface/60 px-3 py-2">
                    <span class="text-sm text-text">Cupón <strong class="text-primary">{{ $coupon->code }}</strong> aplicado</span>
                    <button wire:click="removeCoupon" class="text-sm text-red-400 hover:text-red-300">Quitar</button>
                </div>
            @endif
        </div>

        <div class="mt-6 rounded-xl border border-border bg-surface-muted p-6">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-text/80">
                    <span>Subtotal</span>
                    <span>S/ {{ number_format($subtotal, 2) }}</span>
                </div>
                @if ($discount > 0)
                    <div class="flex justify-between text-primary">
                        <span>Descuento</span>
                        <span>- S/ {{ number_format($discount, 2) }}</span>
                    </div>
                @endif
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
                <p class="font-display text-2xl text-primary">S/ {{ number_format($total, 2) }}</p>
                <a href="{{ route('checkout') }}" wire:navigate class="rounded-lg bg-primary px-8 py-3 font-semibold text-primary-on hover:bg-primary-hover">
                    Proceder al pago
                </a>
            </div>
        </div>
    @endif
</div>
