<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-text-muted/70">Cupones de descuento por monto de compra o por producto específico.</p>
        <button wire:click="create" class="btn-brutal bg-primary px-4 py-2 text-sm font-display font-bold uppercase tracking-wide text-primary-on hover:bg-primary-hover">
            + Nuevo cupón
        </button>
    </div>

    @if ($showForm)
        <div class="card-brutal bg-surface-elevated p-6">
            <h3 class="mb-4 font-display text-lg font-bold uppercase tracking-wide text-text">
                {{ $editingId ? 'Editar cupón' : 'Nuevo cupón' }}
            </h3>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Código</label>
                        <input type="text" wire:model="code" placeholder="CUMBRES10" class="w-full border-[2px] border-text bg-surface px-3 py-2 uppercase text-text focus:border-primary focus:outline-none">
                        @error('code') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Tipo</label>
                        <select wire:model="type" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            <option value="fixed">Monto fijo (S/)</option>
                            <option value="percentage">Porcentaje (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Valor</label>
                        <input type="number" step="0.01" wire:model="value" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        @error('value') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Alcance</label>
                        <select wire:model.live="scope" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            <option value="order">Todo el pedido</option>
                            <option value="product">Un producto específico</option>
                        </select>
                    </div>
                    @if ($scope === 'product')
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Producto</label>
                            <select wire:model="product_id" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                <option value="">Selecciona un producto</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Monto mínimo de compra (opcional)</label>
                        <input type="number" step="0.01" wire:model="min_order_amount" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Usos máximos (opcional)</label>
                        <input type="number" wire:model="max_uses" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-text-muted">Fecha de expiración (opcional)</label>
                        <input type="date" wire:model="expires_at" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    </div>
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_active" class="h-5 w-5 rounded border-[2px] border-text bg-surface text-primary">
                    <span class="text-sm text-text">Cupón activo</span>
                </label>
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
                    <th class="px-4 py-3">Código</th>
                    <th class="px-4 py-3">Descuento</th>
                    <th class="px-4 py-3">Alcance</th>
                    <th class="px-4 py-3">Usos</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-text/15">
                @forelse ($coupons as $coupon)
                    <tr class="bg-surface/40">
                        <td class="px-4 py-3 font-semibold text-text">{{ $coupon->code }}</td>
                        <td class="px-4 py-3 text-text-muted/70">
                            {{ $coupon->type === 'percentage' ? $coupon->value.'%' : 'S/ '.number_format($coupon->value, 2) }}
                        </td>
                        <td class="px-4 py-3 text-text-muted/70">
                            {{ $coupon->scope === 'product' ? ($coupon->product->name ?? 'Producto eliminado') : 'Todo el pedido' }}
                        </td>
                        <td class="px-4 py-3 text-text-muted/70">{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / '.$coupon->max_uses : '' }}</td>
                        <td class="px-4 py-3">
                            <span class="badge-brutal px-2 py-1 text-xs font-display font-bold uppercase tracking-wide {{ $coupon->is_active ? 'bg-green-500/20 text-green-300' : 'bg-surface-muted text-text-muted/60' }}">
                                {{ $coupon->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $coupon->id }})" class="text-primary hover:text-primary-hover">Editar</button>
                            <button wire:click="delete({{ $coupon->id }})" wire:confirm="¿Eliminar este cupón?" class="ml-3 text-red-400 hover:text-red-300">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-text-muted/60">Aún no hay cupones.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
