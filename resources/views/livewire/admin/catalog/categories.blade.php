<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-text-muted/70">Líneas cerveceras que agrupan tus productos.</p>
        <button wire:click="create" class="btn-brutal bg-primary px-4 py-2 text-sm font-display font-bold uppercase tracking-wide text-primary-on hover:bg-primary-hover">
            + Nueva categoría
        </button>
    </div>

    @if ($showForm)
        <div class="card-brutal bg-surface-elevated p-6">
            <h3 class="mb-4 font-display text-lg font-bold uppercase tracking-wide text-text">
                {{ $editingId ? 'Editar categoría' : 'Nueva categoría' }}
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
                <div>
                    <label class="mb-1 block text-sm text-text-muted">Orden</label>
                    <input type="number" wire:model="sort_order" class="w-32 border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
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
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Productos</th>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-text/15">
                @forelse ($categories as $category)
                    <tr class="bg-surface/40">
                        <td class="px-4 py-3 text-text">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $category->products_count }}</td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $category->sort_order }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $category->id }})" class="text-primary hover:text-primary-hover">Editar</button>
                            <button wire:click="delete({{ $category->id }})" wire:confirm="¿Eliminar esta categoría?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-text-muted/60">Aún no hay categorías.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
