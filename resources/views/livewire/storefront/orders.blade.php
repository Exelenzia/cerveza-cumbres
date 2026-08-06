<div class="mx-auto max-w-4xl px-6 py-16">
    <h1 class="mb-10 font-display text-4xl uppercase tracking-wide text-cream-50">Mis pedidos</h1>

    @if ($orders->isEmpty())
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-10 text-center">
            <p class="text-cream-200/70">Todavía no tienes pedidos.</p>
            <a href="{{ route('shop') }}" wire:navigate class="mt-6 inline-block rounded-lg bg-gold-500 px-6 py-3 font-semibold text-cumbre-950 hover:bg-gold-400">
                Ir a la tienda
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" wire:navigate class="flex items-center justify-between rounded-xl border border-cumbre-700 bg-cumbre-900 p-5 transition hover:border-gold-500">
                    <div>
                        <p class="font-display text-lg text-cream-50">{{ $order->order_number }}</p>
                        <p class="text-sm text-cream-200/60">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-cream-100">S/ {{ number_format($order->total, 2) }}</p>
                        <span class="mt-1 inline-block rounded-full bg-gold-500/20 px-3 py-1 text-xs text-gold-400">{{ $order->statusLabel() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
