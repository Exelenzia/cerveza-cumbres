<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-text-muted/70">Páginas informativas armadas con bloques reutilizables (hero, texto+imagen, grid de productos, testimonios, CTA).</p>
        <button wire:click="create" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-on hover:bg-primary-hover">
            + Nueva página
        </button>
    </div>

    @if ($showForm)
        <div class="rounded-xl border border-border bg-surface-elevated p-6">
            <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-text">Nueva página</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm text-text-muted">Título</label>
                    <input type="text" wire:model="title" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none" placeholder="Ej. Nuestra Historia">
                    @error('title') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-on hover:bg-primary-hover">Crear y editar bloques</button>
                    <button type="button" wire:click="cancel" class="rounded-lg border border-border px-4 py-2 text-sm text-text-muted hover:border-primary">Cancelar</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-border">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-elevated text-text-muted/70">
                <tr>
                    <th class="px-4 py-3">Título</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Bloques</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($pages as $page)
                    <tr class="bg-surface/40">
                        <td class="px-4 py-3 text-text">{{ $page->title }}</td>
                        <td class="px-4 py-3 text-text-muted/70">/paginas/{{ $page->slug }}</td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $page->blocks_count }}</td>
                        <td class="px-4 py-3">
                            @if ($page->is_active)
                                <span class="rounded-full bg-green-500/20 px-3 py-1 text-xs font-semibold text-green-400">Activa</span>
                            @else
                                <span class="rounded-full bg-surface-muted px-3 py-1 text-xs font-semibold text-text-muted/60">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.cms.pages.edit', $page) }}" wire:navigate class="text-primary hover:text-primary-hover">Editar bloques</a>
                            <button wire:click="toggleActive({{ $page->id }})" class="ml-3 text-text-muted hover:text-text">
                                {{ $page->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                            <button wire:click="delete({{ $page->id }})" wire:confirm="¿Eliminar esta página y todos sus bloques?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-text-muted/60">Aún no hay páginas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
