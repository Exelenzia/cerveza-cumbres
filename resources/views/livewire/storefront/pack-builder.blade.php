<div class="mx-auto max-w-5xl px-6 py-16">
    <div class="mb-10 text-center">
        <span class="text-sm uppercase tracking-[0.3em] text-gold-400">Arma tu pack</span>
        <h1 class="mt-2 font-display text-4xl uppercase tracking-wide text-cream-50">{{ $template->name }}</h1>
        @if ($template->description)
            <p class="mx-auto mt-3 max-w-2xl text-cream-200/70">{{ $template->description }}</p>
        @endif
    </div>

    @if ($includedMerch)
        <div class="mb-6 rounded-lg border border-gold-500/40 bg-gold-500/10 px-4 py-3 text-center text-sm text-gold-300">
            Incluye gratis: {{ $includedMerch->name }}
        </div>
    @endif

    @if ($template->delivery_note)
        <div class="mb-6 rounded-lg border border-cumbre-700 bg-cumbre-900 px-4 py-3 text-center text-sm text-cream-200/70">
            {{ $template->delivery_note }}
        </div>
    @endif

    @if ($error)
        <div class="mb-6 rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-center text-sm text-red-400">
            {{ $error }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between rounded-lg border border-cumbre-700 bg-cumbre-900 px-5 py-4">
        <p class="text-cream-200">
            Seleccionados: <span class="font-semibold text-cream-50">{{ $totalUnits }}/{{ $template->bottle_count }}</span>
        </p>
        <p class="font-display text-2xl text-gold-400">S/ {{ number_format($price, 2) }}</p>
    </div>

    @if ($template->type === 'fixed_style')
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($eligibleProducts as $product)
                @php $selected = isset($selections[$product->id]); @endphp
                <button
                    type="button"
                    wire:click="increment({{ $product->id }})"
                    class="rounded-xl border p-5 text-left transition {{ $selected ? 'border-gold-500 bg-gold-500/10' : 'border-cumbre-700 bg-cumbre-900 hover:border-gold-500/50' }}"
                >
                    <p class="text-xs uppercase tracking-wide text-gold-400">{{ $product->style }}</p>
                    <h3 class="mt-1 font-display text-lg text-cream-50">{{ $product->name }}</h3>
                    <p class="mt-2 text-sm text-cream-200/70">
                        @if ($product->fixed_pack6_price !== null)
                            S/ {{ number_format($product->fixed_pack6_price, 2) }} por {{ $template->bottle_count }}
                        @else
                            No disponible para este pack
                        @endif
                    </p>
                    @if ($selected)
                        <span class="mt-3 inline-block rounded-full bg-gold-500 px-3 py-1 text-xs font-semibold text-cumbre-950">Elegido</span>
                    @endif
                </button>
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($eligibleProducts as $product)
                @php $qty = $selections[$product->id] ?? 0; @endphp
                <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-5">
                    <p class="text-xs uppercase tracking-wide text-gold-400">{{ $product->style }}</p>
                    <h3 class="mt-1 font-display text-lg text-cream-50">{{ $product->name }}</h3>
                    @if ($product->is_mix_premium && $product->mix_surcharge_per_unit)
                        <p class="mt-1 text-xs text-cream-200/60">+S/ {{ number_format($product->mix_surcharge_per_unit, 2) }} por unidad</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between">
                        <button type="button" wire:click="decrement({{ $product->id }})" class="h-8 w-8 rounded-lg border border-cumbre-700 text-cream-200 hover:border-gold-500">−</button>
                        <span class="font-semibold text-cream-50">{{ $qty }}</span>
                        <button type="button" wire:click="increment({{ $product->id }})" class="h-8 w-8 rounded-lg border border-cumbre-700 text-cream-200 hover:border-gold-500">+</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-10 flex justify-center">
        <button
            wire:click="addToCart"
            @if ($totalUnits !== $template->bottle_count) disabled @endif
            class="rounded-lg px-8 py-3 text-sm font-semibold transition {{ $totalUnits === $template->bottle_count ? 'bg-gold-500 text-cumbre-950 hover:bg-gold-400' : 'cursor-not-allowed bg-cumbre-800 text-cream-100/50' }}"
        >
            Agregar al carrito
        </button>
    </div>
</div>
