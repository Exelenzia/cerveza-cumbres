<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-cream-200/70">Cervezas del catálogo, con precio, stock e imagen.</p>
        <button wire:click="create" class="rounded-lg bg-gold-500 px-4 py-2 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">
            + Nuevo producto
        </button>
    </div>

    @if ($showForm)
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">
                {{ $editingId ? 'Editar producto' : 'Nuevo producto' }}
            </h3>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Nombre</label>
                        <input type="text" wire:model="name" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        @error('name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Categoría</label>
                        <select wire:model="category_id" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                            <option value="">Sin categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Estilo</label>
                        <input type="text" wire:model="style" placeholder="IPA, Stout, Blonde..." class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">ABV %</label>
                            <input type="number" step="0.1" wire:model="abv" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">IBU</label>
                            <input type="number" wire:model="ibu" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">ml</label>
                            <input type="number" wire:model="volume_ml" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm text-cream-200">Descripción</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Precio (S/)</label>
                        <input type="number" step="0.01" wire:model="price" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        @error('price') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Precio anterior</label>
                        <input type="number" step="0.01" wire:model="compare_at_price" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Stock</label>
                        <input type="number" wire:model="stock" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Orden</label>
                        <input type="number" wire:model="sort_order" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 text-sm text-cream-200">
                        <input type="checkbox" wire:model="is_popular" class="rounded border-cumbre-700 bg-cumbre-950 text-gold-500">
                        Popular
                    </label>
                    <label class="flex items-center gap-2 text-sm text-cream-200">
                        <input type="checkbox" wire:model="is_active" class="rounded border-cumbre-700 bg-cumbre-950 text-gold-500">
                        Activo
                    </label>
                </div>

                <div>
                    <label class="mb-1 block text-sm text-cream-200">Imagen</label>
                    <input type="file" wire:model="image" accept="image/*" class="w-full text-sm text-cream-200">
                    @error('image') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-24 w-24 rounded-lg object-cover">
                    @endif
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-lg bg-gold-500 px-4 py-2 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">Guardar</button>
                    <button type="button" wire:click="cancel" class="rounded-lg border border-cumbre-700 px-4 py-2 text-sm text-cream-200 hover:border-gold-500">Cancelar</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-cumbre-700">
        <table class="w-full text-left text-sm">
            <thead class="bg-cumbre-900 text-cream-200/70">
                <tr>
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-4 py-3">Categoría</th>
                    <th class="px-4 py-3">Precio</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cumbre-800">
                @forelse ($products as $product)
                    <tr class="bg-cumbre-950/40">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->cover_url }}" class="h-10 w-10 rounded-lg object-cover">
                                <div>
                                    <p class="text-cream-50">{{ $product->name }}</p>
                                    <p class="text-xs text-cream-200/60">{{ $product->style }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-cream-200/70">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-cream-200/70">S/ {{ number_format($product->price, 2) }}</td>
                        <td class="px-4 py-3 {{ $product->stock <= 10 ? 'text-red-400' : 'text-cream-200/70' }}">{{ $product->stock }}</td>
                        <td class="px-4 py-3">
                            @if ($product->is_active)
                                <span class="rounded-full bg-green-500/20 px-2 py-1 text-xs text-green-400">Activo</span>
                            @else
                                <span class="rounded-full bg-cumbre-700 px-2 py-1 text-xs text-cream-200/60">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $product->id }})" class="text-gold-400 hover:text-gold-300">Editar</button>
                            <button wire:click="delete({{ $product->id }})" wire:confirm="¿Eliminar este producto?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-cream-200/60">Aún no hay productos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
