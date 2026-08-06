<div class="space-y-6">
    <div class="flex flex-wrap items-end gap-4 rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
        <div>
            <label class="block text-xs uppercase tracking-wide text-cream-200/60">Desde</label>
            <input type="date" wire:model="from" class="mt-1 rounded border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-sm text-cream-100" />
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wide text-cream-200/60">Hasta</label>
            <input type="date" wire:model="to" class="mt-1 rounded border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-sm text-cream-100" />
        </div>

        <div class="flex gap-2">
            <button type="button" wire:click="setPreset('7d')" class="rounded border border-cumbre-700 px-3 py-2 text-xs uppercase tracking-wide text-cream-100/80 transition hover:border-gold-500 hover:text-gold-400">7 días</button>
            <button type="button" wire:click="setPreset('30d')" class="rounded border border-cumbre-700 px-3 py-2 text-xs uppercase tracking-wide text-cream-100/80 transition hover:border-gold-500 hover:text-gold-400">30 días</button>
            <button type="button" wire:click="setPreset('90d')" class="rounded border border-cumbre-700 px-3 py-2 text-xs uppercase tracking-wide text-cream-100/80 transition hover:border-gold-500 hover:text-gold-400">90 días</button>
            <button type="button" wire:click="setPreset('month')" class="rounded border border-cumbre-700 px-3 py-2 text-xs uppercase tracking-wide text-cream-100/80 transition hover:border-gold-500 hover:text-gold-400">Este mes</button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <p class="text-sm text-cream-200/70">Ventas del período</p>
            <p class="mt-2 font-display text-3xl text-gold-400">S/ {{ number_format($revenue, 2) }}</p>
        </div>
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <p class="text-sm text-cream-200/70">Pedidos</p>
            <p class="mt-2 font-display text-3xl text-gold-400">{{ $ordersCount }}</p>
        </div>
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <p class="text-sm text-cream-200/70">Ticket promedio</p>
            <p class="mt-2 font-display text-3xl text-gold-400">S/ {{ number_format($averageOrder, 2) }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
        <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">Ventas por día</h3>
        @if ($dailySales->isEmpty())
            <p class="text-cream-200/70">No hay ventas en el período seleccionado.</p>
        @else
            <div class="space-y-2">
                @foreach ($dailySales as $day)
                    <div class="flex items-center gap-3">
                        <span class="w-24 shrink-0 text-xs text-cream-200/60">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d/m/Y') }}</span>
                        <div class="h-3 flex-1 rounded-full bg-cumbre-800">
                            <div class="h-3 rounded-full bg-gold-500" style="width: {{ max(2, round($day['total'] / $maxDaily * 100)) }}%"></div>
                        </div>
                        <span class="w-24 shrink-0 text-right text-xs text-cream-100">S/ {{ number_format($day['total'], 2) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">Productos más vendidos</h3>
            @if ($topProducts->isEmpty())
                <p class="text-cream-200/70">Sin ventas en el período.</p>
            @else
                <div class="space-y-3">
                    @foreach ($topProducts as $row)
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-cream-50">{{ $row->product?->name ?? 'Producto eliminado' }}</span>
                                <span class="text-cream-200/70">{{ $row->units_sold }} u. · S/ {{ number_format($row->revenue, 2) }}</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-cumbre-800">
                                <div class="h-2 rounded-full bg-gold-500" style="width: {{ max(2, round($row->units_sold / $maxUnits * 100)) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">Mejores clientes</h3>
            @if ($topCustomers->isEmpty())
                <p class="text-cream-200/70">Sin clientes en el período.</p>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="text-cream-200/70">
                        <tr>
                            <th class="py-2">Cliente</th>
                            <th class="py-2">Pedidos</th>
                            <th class="py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cumbre-800">
                        @foreach ($topCustomers as $customer)
                            <tr>
                                <td class="py-2">
                                    <p class="text-cream-50">{{ $customer['name'] }}</p>
                                    <p class="text-xs text-cream-200/60">{{ $customer['email'] }}</p>
                                </td>
                                <td class="py-2 text-cream-100">{{ $customer['orders_count'] }}</td>
                                <td class="py-2 text-right text-gold-400">S/ {{ number_format($customer['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
        <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">Stock bajo</h3>
        @if ($lowStockProducts->isEmpty())
            <p class="text-cream-200/70">Todos los productos tienen stock saludable.</p>
        @else
            <table class="w-full text-left text-sm">
                <thead class="text-cream-200/70">
                    <tr>
                        <th class="py-2">Producto</th>
                        <th class="py-2">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cumbre-800">
                    @foreach ($lowStockProducts as $product)
                        <tr>
                            <td class="py-2 text-cream-50">{{ $product->name }}</td>
                            <td class="py-2 text-red-400">{{ $product->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
