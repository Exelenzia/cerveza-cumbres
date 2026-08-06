<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <button wire:click="$set('status', '')" class="rounded-full border px-4 py-1.5 text-sm font-semibold transition {{ $status === '' ? 'border-gold-500 bg-gold-500 text-cumbre-950' : 'border-cumbre-700 text-cream-200 hover:border-gold-500' }}">
            Todos
        </button>
        @foreach ($statuses as $s)
            <button wire:click="$set('status', '{{ $s }}')" class="rounded-full border px-4 py-1.5 text-sm font-semibold capitalize transition {{ $status === $s ? 'border-gold-500 bg-gold-500 text-cumbre-950' : 'border-cumbre-700 text-cream-200 hover:border-gold-500' }}">
                {{ $s }}
            </button>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl border border-cumbre-700">
        <table class="w-full text-left text-sm">
            <thead class="bg-cumbre-900 text-cream-200/70">
                <tr>
                    <th class="px-4 py-3">Pedido</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cumbre-800">
                @forelse ($orders as $order)
                    <tr class="bg-cumbre-950/40">
                        <td class="px-4 py-3 text-cream-50">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-cream-200/70">{{ $order->customer_name }}</td>
                        <td class="px-4 py-3 text-cream-200/70">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-cream-200/70">S/ {{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-gold-500/20 px-2 py-1 text-xs text-gold-400">{{ $order->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" wire:navigate class="text-gold-400 hover:text-gold-300">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-cream-200/60">No hay pedidos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
