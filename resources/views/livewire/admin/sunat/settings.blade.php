<div class="max-w-3xl space-y-6">
    @if (session('sunat-settings-saved'))
        <div class="rounded-lg border border-green-500/40 bg-green-500/10 px-4 py-3 text-sm text-green-300">
            Configuración de SUNAT guardada.
        </div>
    @endif

    @if ($certificadoError)
        <div class="rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-300">
            {{ $certificadoError }}
        </div>
    @endif

    <div class="rounded-xl border border-border bg-surface-elevated p-6">
        <h2 class="mb-1 font-display text-lg uppercase tracking-wide text-text">Datos del emisor</h2>
        <p class="mb-4 text-sm text-text-muted/60">RUC y razón social con los que se emiten los comprobantes electrónicos.</p>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm text-text-muted">RUC</label>
                <input type="text" wire:model="ruc" maxlength="11" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('ruc') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Razón social</label>
                <input type="text" wire:model="razon_social" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('razon_social') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm text-text-muted">Nombre comercial (opcional)</label>
                <input type="text" wire:model="nombre_comercial" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('nombre_comercial') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm text-text-muted">Dirección fiscal</label>
                <input type="text" wire:model="direccion" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('direccion') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Ubigeo (6 dígitos)</label>
                <input type="text" wire:model="ubigeo" maxlength="6" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('ubigeo') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Departamento</label>
                <input type="text" wire:model="departamento" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('departamento') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Provincia</label>
                <input type="text" wire:model="provincia" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('provincia') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Distrito</label>
                <input type="text" wire:model="distrito" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('distrito') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-surface-elevated p-6">
        <h2 class="mb-1 font-display text-lg uppercase tracking-wide text-text">Credenciales SOL y certificado</h2>
        <p class="mb-4 text-sm text-text-muted/60">Usuario secundario SOL y certificado digital (.pfx/.pem) para firmar los comprobantes.</p>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm text-text-muted">Usuario SOL</label>
                <input type="text" wire:model="sol_usuario" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @error('sol_usuario') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Clave SOL</label>
                <input type="password" wire:model="sol_clave" placeholder="{{ $sunatSetting?->sol_usuario ? '••••••••' : '' }}" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                <p class="mt-1 text-xs text-text-muted/50">Déjalo en blanco para conservar la clave actual.</p>
                @error('sol_clave') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Certificado digital (.pfx/.pem)</label>
                <input type="file" wire:model="certificado" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text file:mr-3 file:rounded file:border-0 file:bg-primary file:px-3 file:py-1 file:text-primary-on">
                @if ($sunatSetting?->certificado_path)
                    <p class="mt-1 text-xs text-text-muted/50">Certificado actual cargado. Sube uno nuevo para reemplazarlo.</p>
                @endif
                @error('certificado') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm text-text-muted">Contraseña del certificado</label>
                <input type="password" wire:model="certificado_password" placeholder="{{ $sunatSetting?->certificado_path ? '••••••••' : '' }}" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                <p class="mt-1 text-xs text-text-muted/50">Déjalo en blanco para conservar la contraseña actual.</p>
                @error('certificado_password') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-surface-elevated p-6">
        <h2 class="mb-1 font-display text-lg uppercase tracking-wide text-text">Ambiente</h2>
        <p class="mb-4 text-sm text-text-muted/60">Usa el ambiente beta para pruebas antes de emitir comprobantes reales.</p>

        <select wire:model="modo" class="w-full max-w-xs rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
            <option value="beta">Beta (pruebas)</option>
            <option value="produccion">Producción</option>
        </select>
        @error('modo') <span class="mt-1 block text-sm text-red-400">{{ $message }}</span> @enderror
    </div>

    @if ($series->isNotEmpty())
        <div class="rounded-xl border border-border bg-surface-elevated p-6">
            <h2 class="mb-4 font-display text-lg uppercase tracking-wide text-text">Series de comprobantes</h2>
            <table class="w-full text-left text-sm">
                <thead class="text-text-muted/60">
                    <tr>
                        <th class="pb-2">Tipo</th>
                        <th class="pb-2">Serie</th>
                        <th class="pb-2">Último correlativo</th>
                    </tr>
                </thead>
                <tbody class="text-text">
                    @foreach ($series as $serie)
                        <tr class="border-t border-border">
                            <td class="py-2">{{ $serie->tipo_comprobante === '01' ? 'Factura' : 'Boleta' }}</td>
                            <td class="py-2">{{ $serie->serie }}</td>
                            <td class="py-2">{{ str_pad((string) $serie->correlativo, 8, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <button wire:click="save" class="rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-primary-on hover:bg-primary-hover">
        Guardar cambios
    </button>
</div>
