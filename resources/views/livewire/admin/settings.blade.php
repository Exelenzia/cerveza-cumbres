<div class="max-w-2xl space-y-6">
    @if (session('settings-saved'))
        <div class="rounded-lg border border-green-500/40 bg-green-500/10 px-4 py-3 text-sm text-green-300">
            Configuración guardada.
        </div>
    @endif

    <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
        <h2 class="mb-1 font-display text-lg uppercase tracking-wide text-cream-50">Checkout</h2>
        <p class="mb-4 text-sm text-cream-200/60">Define si los clientes pueden comprar sin crear una cuenta.</p>

        <label class="flex items-center gap-3">
            <input type="checkbox" wire:model="guestCheckoutEnabled" class="h-5 w-5 rounded border-cumbre-700 bg-cumbre-950 text-gold-500">
            <span class="text-sm text-cream-100">Permitir compra sin registro (guest checkout)</span>
        </label>

        <button wire:click="save" class="mt-6 rounded-lg bg-gold-500 px-6 py-2.5 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">
            Guardar cambios
        </button>
    </div>

    <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
        <h2 class="mb-1 font-display text-lg uppercase tracking-wide text-cream-50">Banner de promoción</h2>
        <p class="mb-4 text-sm text-cream-200/60">Mensaje destacado que se muestra arriba de la tienda.</p>

        <label class="mb-4 flex items-center gap-3">
            <input type="checkbox" wire:model="bannerEnabled" class="h-5 w-5 rounded border-cumbre-700 bg-cumbre-950 text-gold-500">
            <span class="text-sm text-cream-100">Mostrar banner</span>
        </label>

        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-sm text-cream-200">Texto</label>
                <input type="text" wire:model="bannerText" placeholder="Envío gratis en Lima por lanzamiento" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                @error('bannerText') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-cream-200">Enlace (opcional)</label>
                <input type="text" wire:model="bannerLink" placeholder="/tienda" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                @error('bannerLink') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>

        <button wire:click="save" class="mt-6 rounded-lg bg-gold-500 px-6 py-2.5 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">
            Guardar cambios
        </button>
    </div>
</div>
