<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-text-muted/70">Cervezas del catálogo, con precio, stock e imagen.</p>
        <button wire:click="create" class="btn-brutal bg-primary px-4 py-2 text-sm font-display font-bold uppercase tracking-wide text-primary-on hover:bg-primary-hover">
            + Nuevo producto
        </button>
    </div>

    @if ($showForm)
        <div class="card-brutal bg-surface-elevated p-6">
            <h3 class="mb-4 font-display text-lg font-bold uppercase tracking-wide text-text">
                {{ $editingId ? 'Editar producto' : 'Nuevo producto' }}
            </h3>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Nombre</label>
                        <input type="text" wire:model="name" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Categoría</label>
                        <select wire:model="category_id" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            <option value="">Sin categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Estilo</label>
                        <input type="text" wire:model="style" placeholder="IPA, Stout, Blonde..." class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">ABV %</label>
                            <input type="number" step="0.1" wire:model="abv" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">IBU</label>
                            <input type="number" wire:model="ibu" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">ml</label>
                            <input type="number" wire:model="volume_ml" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm text-text-muted">Descripción</label>
                    <textarea wire:model="description" rows="3" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Precio (S/)</label>
                        <input type="number" step="0.01" wire:model="price" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('price') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Precio anterior</label>
                        <input type="number" step="0.01" wire:model="compare_at_price" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Stock</label>
                        <input type="number" wire:model="stock" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Orden</label>
                        <input type="number" wire:model="sort_order" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 text-sm text-text-muted">
                        <input type="checkbox" wire:model="is_popular" class="rounded border-[2px] border-text bg-surface text-primary">
                        Popular
                    </label>
                    <label class="flex items-center gap-2 text-sm text-text-muted">
                        <input type="checkbox" wire:model="is_active" class="rounded border-[2px] border-text bg-surface text-primary">
                        Activo
                    </label>
                </div>

                <div>
                    <label class="mb-1 block text-sm text-text-muted">Imagen</label>
                    <input type="file" wire:model="image" accept="image/*" class="w-full text-sm text-text-muted">
                    @error('image') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-24 w-24 object-cover">
                    @endif
                </div>

                <div class="border-[2px] border-text/20 p-4">
                    <h4 class="mb-3 text-sm font-display font-bold uppercase tracking-wide text-text-muted">Precios de pack</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Precio Pack 6 Fijo (S/)</label>
                            <input type="number" step="0.01" wire:model="fixed_pack6_price" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            @error('fixed_pack6_price') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Recargo por unidad en Mix (S/)</label>
                            <input type="number" step="0.01" wire:model="mix_surcharge_per_unit" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            @error('mix_surcharge_per_unit') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 text-sm text-text-muted">
                                <input type="checkbox" wire:model="is_mix_premium" class="rounded border-[2px] border-text bg-surface text-primary">
                                Estilo premium en Mix
                            </label>
                        </div>
                    </div>
                </div>

                <div class="border-[2px] border-text/20 p-4">
                    <label class="flex items-center gap-2 text-sm font-display font-bold uppercase tracking-wide text-text-muted">
                        <input type="checkbox" wire:model.live="has_variants" class="rounded border-[2px] border-text bg-surface text-primary">
                        Este producto tiene variantes (tallas, modelos...)
                    </label>

                    @if ($has_variants)
                        <div class="mt-4 space-y-3">
                            @foreach ($variants as $index => $variant)
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-[2fr_1fr_1fr_auto] md:items-end">
                                    <div>
                                        <label class="mb-1 block text-sm text-text-muted">Etiqueta</label>
                                        <input type="text" wire:model="variants.{{ $index }}.label" placeholder="S, M, L..." class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                        @error("variants.{$index}.label") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm text-text-muted">Precio (opcional)</label>
                                        <input type="number" step="0.01" wire:model="variants.{{ $index }}.price_override" placeholder="Usa el precio base" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm text-text-muted">Stock</label>
                                        <input type="number" wire:model="variants.{{ $index }}.stock" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                    </div>
                                    <button type="button" wire:click="removeVariant({{ $index }})" class="border-[2px] border-text px-3 py-2 text-sm text-red-400 hover:border-red-400">Quitar</button>
                                </div>
                            @endforeach

                            <button type="button" wire:click="addVariant" class="border-[2px] border-text px-3 py-2 text-sm font-display font-bold uppercase tracking-wide text-text-muted hover:border-primary">+ Agregar variante</button>
                        </div>
                    @endif
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-brutal bg-primary px-4 py-2 text-sm font-display font-bold uppercase tracking-wide text-primary-on hover:bg-primary-hover">Guardar</button>
                    <button type="button" wire:click="cancel" class="border-[2px] border-text px-4 py-2 text-sm font-display font-bold uppercase tracking-wide text-text-muted hover:border-primary">Cancelar</button>
                </div>
            </form>
        </div>
    @endif

    <div class="card-brutal overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-elevated font-display font-bold uppercase tracking-wide text-text-muted/70">
                <tr>
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-4 py-3">Categoría</th>
                    <th class="px-4 py-3">Precio</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-text/15">
                @forelse ($products as $product)
                    <tr class="bg-surface/40">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->cover_url }}" class="h-10 w-10 object-cover">
                                <div>
                                    <p class="text-text">{{ $product->name }}</p>
                                    <p class="text-xs text-text-muted/60">{{ $product->style }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-text-muted/70">S/ {{ number_format($product->price, 2) }}</td>
                        <td class="px-4 py-3 {{ $product->stock <= 10 ? 'text-red-400' : 'text-text-muted/70' }}">{{ $product->stock }}</td>
                        <td class="px-4 py-3">
                            @if ($product->is_active)
                                <span class="badge-brutal bg-green-500/20 px-2 py-1 text-xs font-display font-bold uppercase tracking-wide text-green-400">Activo</span>
                            @else
                                <span class="badge-brutal bg-surface-muted px-2 py-1 text-xs font-display font-bold uppercase tracking-wide text-text-muted/60">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $product->id }})" class="text-primary hover:text-primary-hover">Editar</button>
                            <button wire:click="delete({{ $product->id }})" wire:confirm="¿Eliminar este producto?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-text-muted/60">Aún no hay productos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
