<div class="space-y-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <p class="text-sm text-cream-200/70">Categorías</p>
            <p class="mt-2 font-display text-3xl text-gold-400">{{ $categoriesCount }}</p>
        </div>
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <p class="text-sm text-cream-200/70">Productos</p>
            <p class="mt-2 font-display text-3xl text-gold-400">{{ $productsCount }}</p>
        </div>
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <p class="text-sm text-cream-200/70">Packs</p>
            <p class="mt-2 font-display text-3xl text-gold-400">{{ $packsCount }}</p>
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
