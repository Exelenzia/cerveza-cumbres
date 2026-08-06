<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-cream-200/70">Zonas y tarifas de envío. Las ciudades se comparan con lo que el cliente escriba en el checkout.</p>
        <button wire:click="create" class="rounded-lg bg-gold-500 px-4 py-2 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">
            + Nueva zona
        </button>
    </div>

    @if ($showForm)
        <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">
                {{ $editingId ? 'Editar zona' : 'Nueva zona' }}
            </h3>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Nombre</label>
                        <input type="text" wire:model="name" placeholder="Lima Metropolitana" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        @error('name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Precio (S/)</label>
                        <input type="number" step="0.01" wire:model="price" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        @error('price') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm text-cream-200">Ciudades (separadas por coma)</label>
                    <textarea wire:model="cities" rows="2" placeholder="Lima, Callao, San Isidro" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none"></textarea>
                    @error('cities') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Tiempo estimado</label>
                        <input type="text" wire:model="eta_label" placeholder="24 horas" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-cream-200">Orden</label>
                        <input type="number" wire:model="sort_order" class="w-32 rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    </div>
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_active" class="h-5 w-5 rounded border-cumbre-700 bg-cumbre-950 text-gold-500">
                    <span class="text-sm text-cream-100">Zona activa</span>
                </label>
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
                    <th class="px-4 py-3">Ciudades</th>
                    <th class="px-4 py-3">Precio</th>
                    <th class="px-4 py-3">Tiempo</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-cumbre-800">
                @forelse ($zones as $zone)
                    <tr class="bg-cumbre-950/40">
                        <td class="px-4 py-3 text-cream-50">{{ $zone->name }}</td>
                        <td class="px-4 py-3 text-cream-200/70">{{ $zone->cities }}</td>
                        <td class="px-4 py-3 text-cream-200/70">S/ {{ number_format($zone->price, 2) }}</td>
                        <td class="px-4 py-3 text-cream-200/70">{{ $zone->eta_label ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs {{ $zone->is_active ? 'bg-green-500/20 text-green-300' : 'bg-cumbre-700 text-cream-200/60' }}">
                                {{ $zone->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $zone->id }})" class="text-gold-400 hover:text-gold-300">Editar</button>
                            <button wire:click="delete({{ $zone->id }})" wire:confirm="¿Eliminar esta zona?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-cream-200/60">Aún no hay zonas de envío. El envío será S/0 hasta que crees una.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
