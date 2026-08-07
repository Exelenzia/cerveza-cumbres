<div class="mx-auto max-w-7xl px-6 py-16">
    <div class="mb-10 text-center" data-reveal>
        <span class="text-sm uppercase tracking-[0.3em] text-secondary">Catálogo</span>
        <h1 class="mt-2 font-display text-4xl font-bold uppercase tracking-wide text-text">Tienda</h1>
    </div>

    @if ($categories->isNotEmpty())
        <div class="mb-10 flex flex-wrap justify-center gap-3" data-reveal>
            <button
                wire:click="selectCategory(null)"
                class="badge-brutal px-5 py-2 text-sm font-bold transition {{ is_null($category) ? 'bg-primary text-primary-on' : 'bg-surface-elevated text-text hover:bg-primary hover:text-primary-on' }}"
            >
                Todas
            </button>
            @foreach ($categories as $cat)
                <button
                    wire:click="selectCategory({{ $cat->id }})"
                    class="badge-brutal px-5 py-2 text-sm font-bold transition {{ $category === $cat->id ? 'bg-primary text-primary-on' : 'bg-surface-elevated text-text hover:bg-primary hover:text-primary-on' }}"
                >
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($products as $product)
            @php
                $activeVariants = $product->variants->where('is_active', true);
                $outOfStock = $product->has_variants ? $activeVariants->sum('stock') <= 0 : $product->stock <= 0;
            @endphp
            <div class="card-brutal-interactive group overflow-hidden" data-reveal>
                <div class="relative aspect-square overflow-hidden border-b-[3px] border-text">
                    <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                    @if ($product->is_popular)
                        <span class="badge-brutal absolute left-3 top-3 bg-primary px-3 py-1 text-xs font-bold text-primary-on">Popular</span>
                    @endif
                    @if ($product->discount_percent)
                        <span class="badge-brutal absolute right-3 top-3 bg-red-500 px-3 py-1 text-xs font-bold text-white">-{{ $product->discount_percent }}%</span>
                    @endif
                    @if ($outOfStock)
                        <span class="absolute inset-x-3 bottom-3 border-[2px] border-text bg-surface/90 px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-text">Agotado</span>
                    @endif
                </div>
                <div class="p-5">
                    <p class="text-xs uppercase tracking-wide text-secondary">{{ $product->style }} @if($product->volume_ml) · {{ $product->volume_ml }} ml @endif</p>
                    <h3 class="mt-1 font-display text-xl text-text">{{ $product->name }}</h3>
                    @if ($product->description)
                        <p class="mt-2 line-clamp-2 text-sm text-text/70">{{ $product->description }}</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between gap-3" @if($product->has_variants) x-data="{ variantId: {{ $activeVariants->first()->id ?? 'null' }} }" @endif>
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-text">S/ {{ number_format($product->price, 2) }}</p>
                            @if ($product->compare_at_price)
                                <p class="text-sm text-text/50 line-through">S/ {{ number_format($product->compare_at_price, 2) }}</p>
                            @endif
                        </div>
                        @if ($product->has_variants)
                            <div class="flex items-center gap-2">
                                <select x-model.number="variantId" class="border-[2px] border-text bg-surface px-2 py-1 text-xs text-text focus:border-primary focus:outline-none">
                                    @foreach ($activeVariants as $variant)
                                        <option value="{{ $variant->id }}" @if ($variant->stock <= 0) disabled @endif>
                                            {{ $variant->label }}@if ($variant->stock <= 0) (agotado)@endif
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" @click="$wire.addToCart('product', {{ $product->id }}, variantId)" class="btn-brutal bg-primary px-3 py-1.5 text-xs font-bold text-primary-on">
                                    Agregar
                                </button>
                            </div>
                        @elseif ($product->stock > 0)
                            <button wire:click="addToCart('product', {{ $product->id }})" class="btn-brutal bg-primary px-3 py-1.5 text-xs font-bold text-primary-on">
                                Agregar
                            </button>
                        @else
                            <button type="button" disabled class="cursor-not-allowed border-[3px] border-text/30 bg-surface-muted px-3 py-1.5 text-xs font-bold text-text/50">
                                Agotado
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-text/60">No hay productos en esta línea todavía.</p>
        @endforelse
    </div>

    @if ($packs->isNotEmpty())
        <div class="mt-16">
            <h2 class="mb-8 text-center font-display text-3xl font-bold uppercase tracking-wide text-text" data-reveal>Packs</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($packs as $pack)
                    <div class="card-brutal-interactive overflow-hidden" data-reveal>
                        <div class="relative aspect-square overflow-hidden border-b-[3px] border-text">
                            <img src="{{ $pack->cover_url }}" alt="{{ $pack->name }}" class="h-full w-full object-cover">
                            @if ($pack->discount_percent)
                                <span class="badge-brutal absolute right-3 top-3 bg-red-500 px-3 py-1 text-xs font-bold text-white">-{{ $pack->discount_percent }}%</span>
                            @endif
                            @if ($pack->is_out_of_stock)
                                <span class="absolute inset-x-3 bottom-3 border-[2px] border-text bg-surface/90 px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-text">Agotado</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-display text-xl text-text">{{ $pack->name }}</h3>
                            @if ($pack->description)
                                <p class="mt-2 line-clamp-2 text-sm text-text/70">{{ $pack->description }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <p class="font-semibold text-text">S/ {{ number_format($pack->price, 2) }}</p>
                                    @if ($pack->compare_at_price)
                                        <p class="text-sm text-text/50 line-through">S/ {{ number_format($pack->compare_at_price, 2) }}</p>
                                    @endif
                                </div>
                                @if (! $pack->is_out_of_stock)
                                    <button wire:click="addToCart('pack', {{ $pack->id }})" class="btn-brutal bg-primary px-3 py-1.5 text-xs font-bold text-primary-on">
                                        Agregar
                                    </button>
                                @else
                                    <button type="button" disabled class="cursor-not-allowed border-[3px] border-text/30 bg-surface-muted px-3 py-1.5 text-xs font-bold text-text/50">
                                        Agotado
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($packTemplates->isNotEmpty())
        <div class="mt-16">
            <h2 class="mb-8 text-center font-display text-3xl font-bold uppercase tracking-wide text-text" data-reveal>Arma tu pack</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($packTemplates as $template)
                    <a href="{{ route('pack-builder.show', $template) }}" class="card-brutal-interactive block p-5" data-reveal>
                        <h3 class="font-display text-xl text-text">{{ $template->name }}</h3>
                        @if ($template->description)
                            <p class="mt-2 line-clamp-2 text-sm text-text/70">{{ $template->description }}</p>
                        @endif
                        <p class="mt-4 text-sm text-secondary">{{ $template->bottle_count }} unidades · arma tu selección</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
