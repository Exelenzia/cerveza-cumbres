<div class="max-w-2xl space-y-6">
    @if (session('settings-saved'))
        <div class="rounded-lg border border-green-500/40 bg-green-500/10 px-4 py-3 text-sm text-green-300">
            Configuración guardada.
        </div>
    @endif

    <div class="rounded-xl border border-border bg-surface-elevated p-6">
        <h2 class="mb-1 font-display text-lg uppercase tracking-wide text-text">Checkout</h2>
        <p class="mb-4 text-sm text-text-muted/60">Define si los clientes pueden comprar sin crear una cuenta.</p>

        <label class="flex items-center gap-3">
            <input type="checkbox" wire:model="guestCheckoutEnabled" class="h-5 w-5 rounded border-border bg-surface text-primary">
            <span class="text-sm text-text">Permitir compra sin registro (guest checkout)</span>
        </label>

        <button wire:click="save" class="mt-6 rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-primary-on hover:bg-primary-hover">
            Guardar cambios
        </button>
    </div>

    <div class="rounded-xl border border-border bg-surface-elevated p-6">
        <h2 class="mb-1 font-display text-lg uppercase tracking-wide text-text">Banner de promoción</h2>
        <p class="mb-4 text-sm text-text-muted/60">Mensaje destacado que se muestra arriba de la tienda.</p>

        <label class="mb-4 flex items-center gap-3">
            <input type="checkbox" wire:model="bannerEnabled" class="h-5 w-5 rounded border-border bg-surface text-primary">
            <span class="text-sm text-text">Mostrar banner</span>
        </label>

        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-sm text-text-muted">Texto</label>
                <input type="text" wire:model="bannerText" placeholder="Envío gratis en Lima por lanzamiento" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('bannerText') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Enlace (opcional)</label>
                <input type="text" wire:model="bannerLink" placeholder="/tienda" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('bannerLink') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>

        <button wire:click="save" class="mt-6 rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-primary-on hover:bg-primary-hover">
            Guardar cambios
        </button>
    </div>
</div>
