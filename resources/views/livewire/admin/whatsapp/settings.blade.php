<div class="max-w-3xl space-y-6">
    @if (session('whatsapp-settings-saved'))
        <div class="card-brutal border-green-500/40 bg-green-500/10 px-4 py-3 text-sm text-green-300">
            Configuración de WhatsApp guardada.
        </div>
    @endif

    <div class="card-brutal bg-surface-elevated p-6">
        <h2 class="mb-1 font-display text-lg font-bold uppercase tracking-wide text-text">Meta WhatsApp Cloud API</h2>
        <p class="mb-4 text-sm text-text-muted/60">Credenciales del número de WhatsApp Business configurado en Meta Business Manager.</p>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm text-text-muted">Phone Number ID</label>
                <input type="text" wire:model="phone_number_id" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('phone_number_id') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Business Account ID (opcional)</label>
                <input type="text" wire:model="business_account_id" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('business_account_id') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm text-text-muted">Access Token</label>
                <input type="password" wire:model="access_token" placeholder="{{ $whatsappSetting?->phone_number_id ? '••••••••' : '' }}" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                <p class="mt-1 text-xs text-text-muted/50">Déjalo en blanco para conservar el token actual.</p>
                @error('access_token') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Número "clic para chatear"</label>
                <input type="text" wire:model="wa_link_phone" placeholder="51999999999" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                <p class="mt-1 text-xs text-text-muted/50">Con código de país, sin "+". Se usa en el botón de la tienda.</p>
                @error('wa_link_phone') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Idioma de plantillas</label>
                <input type="text" wire:model="template_language" placeholder="es" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('template_language') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="card-brutal bg-surface-elevated p-6">
        <h2 class="mb-1 font-display text-lg font-bold uppercase tracking-wide text-text">Plantillas de notificación</h2>
        <p class="mb-4 text-sm text-text-muted/60">Nombres exactos de las plantillas aprobadas por Meta para cada evento de pedido. Déjalas en blanco para desactivar esa notificación.</p>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm text-text-muted">Pedido confirmado</label>
                <input type="text" wire:model="template_confirmacion" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('template_confirmacion') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Pedido enviado</label>
                <input type="text" wire:model="template_enviado" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('template_enviado') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Pedido entregado</label>
                <input type="text" wire:model="template_entregado" class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('template_entregado') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="card-brutal bg-surface-elevated p-6">
        <label class="flex items-center gap-3 text-sm text-text-muted">
            <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded border-[2px] border-text bg-surface text-primary focus:ring-primary">
            Integración activa
        </label>
    </div>

    <button wire:click="save" class="btn-brutal bg-primary px-6 py-2.5 text-sm font-display font-bold uppercase tracking-wide text-primary-on hover:bg-primary-hover">
        Guardar cambios
    </button>
</div>
