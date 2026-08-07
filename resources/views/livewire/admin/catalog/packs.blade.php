<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-text-muted/70">Combos de productos con precio especial.</p>
        <button wire:click="create" class="btn-brutal bg-primary px-4 py-2 text-sm font-display font-bold uppercase tracking-wide text-primary-on hover:bg-primary-hover">
            + Nuevo pack
        </button>
    </div>

    @if ($showForm)
        <div class="card-brutal bg-surface-elevated p-6">
            <h3 class="mb-4 font-display text-lg font-bold uppercase tracking-wide text-text">
                {{ $editingId ? 'Editar pack' : 'Nuevo pack' }}
            </h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm text-text-muted">Nombre</label>
                    <input type="text" wire:model="name" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    @error('name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm text-text-muted">Descripción</label>
                    <textarea wire:model="description" rows="3" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
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
                        <label class="mb-1 block text-sm text-text-muted">Orden</label>
                        <input type="number" wire:model="sort_order" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-text-muted">
                    <input type="checkbox" wire:model="is_active" class="rounded border-[2px] border-text bg-surface text-primary">
                    Activo
                </label>

                <div>
                    <label class="mb-1 block text-sm text-text-muted">Imagen</label>
                    <input type="file" wire:model="image" accept="image/*" class="w-full text-sm text-text-muted">
                    @error('image') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-24 w-24 object-cover">
                    @endif
                </div>

                <div>
                    <label class="mb-2 block text-sm text-text-muted">Productos incluidos</label>
                    <div class="space-y-2">
                        @foreach ($items as $index => $item)
                            <div class="flex items-center gap-3">
                                <select wire:model="items.{{ $index }}.product_id" class="flex-1 border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                    <option value="">Selecciona un producto</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" min="1" wire:model="items.{{ $index }}.quantity" class="w-24 border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-300">Quitar</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" wire:click="addItem" class="mt-3 text-sm text-primary hover:text-primary-hover">+ Agregar producto</button>
                    @error('items.*.product_id') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
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
                    <th class="px-4 py-3">Pack</th>
                    <th class="px-4 py-3">Productos</th>
                    <th class="px-4 py-3">Precio</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-text/15">
                @forelse ($packs as $pack)
                    <tr class="bg-surface/40">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $pack->cover_url }}" class="h-10 w-10 object-cover">
                                <p class="text-text">{{ $pack->name }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $pack->products->pluck('name')->join(', ') }}</td>
                        <td class="px-4 py-3 text-text-muted/70">S/ {{ number_format($pack->price, 2) }}</td>
                        <td class="px-4 py-3">
                            @if ($pack->is_active)
                                <span class="badge-brutal bg-green-500/20 px-2 py-1 text-xs font-display font-bold uppercase tracking-wide text-green-400">Activo</span>
                            @else
                                <span class="badge-brutal bg-surface-muted px-2 py-1 text-xs font-display font-bold uppercase tracking-wide text-text-muted/60">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $pack->id }})" class="text-primary hover:text-primary-hover">Editar</button>
                            <button wire:click="delete({{ $pack->id }})" wire:confirm="¿Eliminar este pack?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-text-muted/60">Aún no hay packs.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
