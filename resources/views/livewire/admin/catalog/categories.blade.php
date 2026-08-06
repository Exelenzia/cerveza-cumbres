<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-cream-200/70">Líneas cerveceras que agrupan tus productos.</p>
        <button wire:click="create" class="rounded-lg bg-gold-500 px-4 py-2 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">
            + Nueva categoría
        </button>
    </div>

    @if ($showForm)
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">
                {{ $editingId ? 'Editar categoría' : 'Nueva categoría' }}
            </h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm text-cream-200">Nombre</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    @error('name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm text-cream-200">Descripción</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm text-cream-200">Orden</label>
                    <input type="number" wire:model="sort_order" class="w-32 rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
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
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Productos</th>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cumbre-800">
                @forelse ($categories as $category)
                    <tr class="bg-cumbre-950/40">
                        <td class="px-4 py-3 text-cream-50">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-cream-200/70">{{ $category->products_count }}</td>
                        <td class="px-4 py-3 text-cream-200/70">{{ $category->sort_order }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $category->id }})" class="text-gold-400 hover:text-gold-300">Editar</button>
                            <button wire:click="delete({{ $category->id }})" wire:confirm="¿Eliminar esta categoría?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-cream-200/60">Aún no hay categorías.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
