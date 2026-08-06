<div class="mx-auto max-w-3xl px-6 py-16">
    <div class="mb-10 text-center">
        <p class="text-sm uppercase tracking-[0.3em] text-secondary">Pedido</p>
        <h1 class="mt-2 font-display text-4xl uppercase tracking-wide text-text">{{ $order->order_number }}</h1>
        <span class="mt-4 inline-block rounded-full bg-primary/20 px-4 py-1.5 text-sm text-primary">{{ $order->statusLabel() }}</span>
    </div>

    <div class="rounded-xl border border-border bg-surface-muted p-6">
        <h2 class="mb-4 font-display text-lg uppercase tracking-wide text-text">Productos</h2>
        <div class="space-y-3">
            @foreach ($items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-text/80">{{ $item->name }} × {{ $item->quantity }}</span>
                    <span class="text-text">S/ {{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>
        <div class="mt-4 space-y-2 border-t border-border pt-4 text-sm">
            <div class="flex justify-between text-text/80">
                <span>Subtotal</span>
                <span>S/ {{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-text/80">
                <span>Envío</span>
                <span>S/ {{ number_format($order->shipping_cost, 2) }}</span>
            </div>
            <div class="flex justify-between font-display text-lg text-primary">
                <span>Total</span>
                <span>S/ {{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="rounded-xl border border-border bg-surface-muted p-6">
            <h2 class="mb-3 font-display text-lg uppercase tracking-wide text-text">Entrega</h2>
            <p class="text-sm text-text/80">{{ $order->customer_name }}</p>
            <p class="text-sm text-text/80">{{ $order->shipping_address }}</p>
            <p class="text-sm text-text/80">{{ $order->shipping_city }}</p>
            @if ($order->customer_phone)
                <p class="mt-2 text-sm text-text/60">Tel: {{ $order->customer_phone }}</p>
            @endif
        </div>
        <div class="rounded-xl border border-border bg-surface-muted p-6">
            <h2 class="mb-3 font-display text-lg uppercase tracking-wide text-text">Pago</h2>
            <p class="text-sm text-text/80">Método: Culqi (tarjeta)</p>
            @if ($order->paid_at)
                <p class="text-sm text-text/80">Pagado el {{ $order->paid_at->format('d/m/Y H:i') }}</p>
            @endif
            <p class="mt-2 text-sm text-text/60">Ref: {{ $order->payment_reference }}</p>
        </div>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('shop') }}" wire:navigate class="text-primary hover:text-primary-hover">Seguir comprando</a>
    </div>
</div>
