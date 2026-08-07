<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <button wire:click="$set('status', '')" class="border-[2px] px-4 py-1.5 text-sm font-display font-bold uppercase tracking-wide transition {{ $status === '' ? 'border-text bg-primary text-primary-on' : 'border-text/30 text-text-muted hover:border-text' }}">
            Todos
        </button>
        @foreach ($statuses as $s)
            <button wire:click="$set('status', '{{ $s }}')" class="border-[2px] px-4 py-1.5 text-sm font-display font-bold uppercase tracking-wide transition {{ $status === $s ? 'border-text bg-primary text-primary-on' : 'border-text/30 text-text-muted hover:border-text' }}">
                {{ $s }}
            </button>
        @endforeach
    </div>

    <div class="card-brutal overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-elevated font-display font-bold uppercase tracking-wide text-text-muted/70">
                <tr>
                    <th class="px-4 py-3">Pedido</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-text/15">
                @forelse ($orders as $order)
                    <tr class="bg-surface/40">
                        <td class="px-4 py-3 text-text">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $order->customer_name }}</td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-text-muted/70">S/ {{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="badge-brutal bg-primary/20 px-2 py-1 text-xs font-display font-bold uppercase tracking-wide text-primary">{{ $order->statusLabel() }}</span>
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
