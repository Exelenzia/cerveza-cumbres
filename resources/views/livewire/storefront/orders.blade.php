<div class="mx-auto max-w-4xl px-6 py-16">
    <h1 class="mb-10 font-display text-4xl uppercase tracking-wide text-text" data-reveal>Mis pedidos</h1>

    @if ($orders->isEmpty())
        <div class="card-brutal p-10 text-center">
            <p class="text-text/70">Todavía no tienes pedidos.</p>
            <a href="{{ route('shop') }}" wire:navigate class="btn-brutal mt-6 inline-flex bg-primary px-6 py-3 font-semibold text-primary-on hover:bg-primary-hover">
                Ir a la tienda
            </a>
        </div>
    @else
        <div class="space-y-4" data-reveal>
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" wire:navigate class="card-brutal-interactive flex items-center justify-between p-5">
                    <div>
                        <p class="font-display text-lg text-text">{{ $order->order_number }}</p>
                        <p class="text-sm text-text/60">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-text">S/ {{ number_format($order->total, 2) }}</p>
                        <span class="badge-brutal mt-1 bg-primary/20 px-3 py-1 text-xs font-bold text-primary">{{ $order->statusLabel() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
