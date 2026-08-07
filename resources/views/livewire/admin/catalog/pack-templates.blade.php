<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-text-muted/70">Plantillas de packs armados por el cliente (Pack 6 Fijo, Mix, Regalo).</p>
        <button wire:click="create" class="btn-brutal bg-primary px-4 py-2 text-sm font-display font-bold uppercase tracking-wide text-primary-on hover:bg-primary-hover">
            + Nueva plantilla
        </button>
    </div>

    @if ($showForm)
        <div class="card-brutal bg-surface-elevated p-6">
            <h3 class="mb-4 font-display text-lg font-bold uppercase tracking-wide text-text">
                {{ $editingId ? 'Editar plantilla' : 'Nueva plantilla' }}
            </h3>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Nombre</label>
                        <input type="text" wire:model="name" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Tipo</label>
                        <select wire:model="type" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            <option value="fixed_style">Fijo (un solo estilo)</option>
                            <option value="mix">Mix (mezcla libre)</option>
                            <option value="gift">Regalo</option>
                        </select>
                        @error('type') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm text-text-muted">Descripción</label>
                    <textarea wire:model="description" rows="2" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Unidades de cerveza</label>
                        <input type="number" min="1" wire:model="bottle_count" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('bottle_count') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Precio base (S/)</label>
                        <input type="number" step="0.01" wire:model="base_price" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        <p class="mt-1 text-xs text-text-muted/50">No aplica en tipo "Fijo" — ese precio sale del campo "Precio Pack 6 Fijo" de cada producto.</p>
                        @error('base_price') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Orden</label>
                        <input type="number" wire:model="sort_order" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                </div>

                @if ($type === 'gift')
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Merch incluido (gratis)</label>
                        <select wire:model="included_merch_product_id" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            <option value="">Ninguno</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Costo de envío del pack (S/)</label>
                        <input type="number" step="0.01" wire:model="delivery_cost" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        <p class="mt-1 text-xs text-text-muted/50">Informativo — el costo cobrado sigue viniendo de la zona de envío elegida en checkout.</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Nota de envío</label>
                        <input type="text" wire:model="delivery_note" placeholder="Ej. Sujeto a disponibilidad de ruta" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-text-muted">
                    <input type="checkbox" wire:model="free_shipping_eligible" class="rounded border-[2px] border-text bg-surface text-primary">
                    Aplica envío gratis desde S/250
                </label>

                <label class="flex items-center gap-2 text-sm text-text-muted">
                    <input type="checkbox" wire:model="is_active" class="rounded border-[2px] border-text bg-surface text-primary">
                    Activo
                </label>

                <div>
                    <label class="mb-2 block text-sm text-text-muted">Estilos elegibles para este pack</label>
                    <div class="grid max-h-64 grid-cols-1 gap-2 overflow-y-auto border-[2px] border-text bg-surface p-3 sm:grid-cols-2">
                        @foreach ($products as $product)
                            <label class="flex items-center gap-2 text-sm text-text-muted">
                                <input type="checkbox" wire:model="eligible_product_ids" value="{{ $product->id }}" class="rounded border-[2px] border-text bg-surface text-primary">
                                {{ $product->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('eligible_product_ids') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
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
                    <th class="px-4 py-3">Plantilla</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Unidades</th>
                    <th class="px-4 py-3">Estilos elegibles</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-text/15">
                @forelse ($templates as $template)
                    <tr class="bg-surface/40">
                        <td class="px-4 py-3 text-text">{{ $template->name }}</td>
                        <td class="px-4 py-3 text-text-muted/70">
                            {{ ['fixed_style' => 'Fijo', 'mix' => 'Mix', 'gift' => 'Regalo'][$template->type] ?? $template->type }}
                        </td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $template->bottle_count }}</td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $template->eligibleProducts->count() }} estilos</td>
                        <td class="px-4 py-3">
                            @if ($template->is_active)
                                <span class="badge-brutal bg-green-500/20 px-2 py-1 text-xs font-display font-bold uppercase tracking-wide text-green-400">Activo</span>
                            @else
                                <span class="badge-brutal bg-surface-muted px-2 py-1 text-xs font-display font-bold uppercase tracking-wide text-text-muted/60">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $template->id }})" class="text-primary hover:text-primary-hover">Editar</button>
                            <button wire:click="delete({{ $template->id }})" wire:confirm="¿Eliminar esta plantilla?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-text-muted/60">Aún no hay plantillas de pack.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
