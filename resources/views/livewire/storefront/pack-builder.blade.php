<div class="mx-auto max-w-5xl px-6 py-16">
    <div class="mb-10 text-center">
        <span class="text-sm uppercase tracking-[0.3em] text-secondary">Arma tu pack</span>
        <h1 class="mt-2 font-display text-4xl uppercase tracking-wide text-text" data-reveal>{{ $template->name }}</h1>
        @if ($template->description)
            <p class="mx-auto mt-3 max-w-2xl text-text/70">{{ $template->description }}</p>
        @endif
    </div>

    @if ($includedMerch)
        <div class="mb-6 border-[2px] border-primary/40 bg-primary/10 px-4 py-3 text-center text-sm text-primary">
            Incluye gratis: {{ $includedMerch->name }}
        </div>
    @endif

    @if ($template->delivery_note)
        <div class="card-brutal mb-6 px-4 py-3 text-center text-sm text-text/70">
            {{ $template->delivery_note }}
        </div>
    @endif

    @if ($error)
        <div class="mb-6 border-[2px] border-red-500/40 bg-red-500/10 px-4 py-3 text-center text-sm text-red-400">
            {{ $error }}
        </div>
    @endif

    <div class="card-brutal mb-6 flex items-center justify-between px-5 py-4">
        <p class="text-text">
            Seleccionados: <span class="font-semibold text-text">{{ $totalUnits }}/{{ $template->bottle_count }}</span>
        </p>
        <p class="font-display text-2xl text-primary">S/ {{ number_format($price, 2) }}</p>
    </div>

    @if ($template->type === 'fixed_style')
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" data-reveal>
            @foreach ($eligibleProducts as $product)
                @php $selected = isset($selections[$product->id]); @endphp
                <button
                    type="button"
                    wire:click="increment({{ $product->id }})"
                    class="card-brutal-interactive p-5 text-left transition {{ $selected ? 'border-primary bg-primary/10' : '' }}"
                >
                    <p class="text-xs uppercase tracking-wide text-secondary">{{ $product->style }}</p>
                    <h3 class="mt-1 font-display text-lg text-text">{{ $product->name }}</h3>
                    <p class="mt-2 text-sm text-text/70">
                        @if ($product->fixed_pack6_price !== null)
                            S/ {{ number_format($product->fixed_pack6_price, 2) }} por {{ $template->bottle_count }}
                        @else
                            No disponible para este pack
                        @endif
                    </p>
                    @if ($selected)
                        <span class="badge-brutal mt-3 bg-primary px-3 py-1 text-xs font-bold text-primary-on">Elegido</span>
                    @endif
                </button>
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" data-reveal>
            @foreach ($eligibleProducts as $product)
                @php $qty = $selections[$product->id] ?? 0; @endphp
                <div class="card-brutal p-5">
                    <p class="text-xs uppercase tracking-wide text-secondary">{{ $product->style }}</p>
                    <h3 class="mt-1 font-display text-lg text-text">{{ $product->name }}</h3>
                    @if ($product->is_mix_premium && $product->mix_surcharge_per_unit)
                        <p class="mt-1 text-xs text-text/60">+S/ {{ number_format($product->mix_surcharge_per_unit, 2) }} por unidad</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between">
                        <button type="button" wire:click="decrement({{ $product->id }})" class="btn-brutal h-8 w-8 bg-surface-elevated text-text">−</button>
                        <span class="font-semibold text-text">{{ $qty }}</span>
                        <button type="button" wire:click="increment({{ $product->id }})" class="btn-brutal h-8 w-8 bg-surface-elevated text-text">+</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-10 flex justify-center">
        <button
            wire:click="addToCart"
            @if ($totalUnits !== $template->bottle_count) disabled @endif
            class="px-8 py-3 text-sm font-semibold transition {{ $totalUnits === $template->bottle_count ? 'btn-brutal bg-primary text-primary-on hover:bg-primary-hover' : 'cursor-not-allowed border-[3px] border-text/30 bg-surface-muted text-text/50' }}"
        >
            Agregar al carrito
        </button>
    </div>
</div>
