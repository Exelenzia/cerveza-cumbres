<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <button wire:click="$set('status', '')" class="rounded-full border px-4 py-1.5 text-sm font-semibold transition {{ $status === '' ? 'border-primary bg-primary text-primary-on' : 'border-border text-text-muted hover:border-primary' }}">
            Todos
        </button>
        @foreach ($statuses as $s)
            <button wire:click="$set('status', '{{ $s }}')" class="rounded-full border px-4 py-1.5 text-sm font-semibold capitalize transition {{ $status === $s ? 'border-primary bg-primary text-primary-on' : 'border-border text-text-muted hover:border-primary' }}">
                {{ $s }}
            </button>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl border border-border">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-elevated text-text-muted/70">
                <tr>
                    <th class="px-4 py-3">Pedido</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($orders as $order)
                    <tr class="bg-surface/40">
                        <td class="px-4 py-3 text-text">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $order->customer_name }}</td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-text-muted/70">S/ {{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-primary/20 px-2 py-1 text-xs text-primary">{{ $order->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" wire:navigate class="text-primary hover:text-primary-hover">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-text-muted/60">No hay pedidos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
