<div class="space-y-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-border bg-surface-muted p-6">
            <p class="text-sm text-text-muted/70">Categorías</p>
            <p class="mt-2 font-display text-3xl text-primary">{{ $categoriesCount }}</p>
        </div>
        <div class="rounded-xl border border-border bg-surface-muted p-6">
            <p class="text-sm text-text-muted/70">Productos</p>
            <p class="mt-2 font-display text-3xl text-primary">{{ $productsCount }}</p>
        </div>
        <div class="rounded-xl border border-border bg-surface-muted p-6">
            <p class="text-sm text-text-muted/70">Packs</p>
            <p class="mt-2 font-display text-3xl text-primary">{{ $packsCount }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-surface-muted p-6">
        <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-text">Stock bajo</h3>
        @if ($lowStockProducts->isEmpty())
            <p class="text-text-muted/70">Todos los productos tienen stock saludable.</p>
        @else
            <table class="w-full text-left text-sm">
                <thead class="text-text-muted/70">
                    <tr>
                        <th class="py-2">Producto</th>
                        <th class="py-2">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($lowStockProducts as $product)
                        <tr>
                            <td class="py-2 text-text">{{ $product->name }}</td>
                            <td class="py-2 text-red-400">{{ $product->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
