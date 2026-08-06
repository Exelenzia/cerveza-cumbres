<div class="mx-auto max-w-7xl px-6 py-16">
    <div class="mb-10 text-center">
        <span class="text-sm uppercase tracking-[0.3em] text-gold-400">Catálogo</span>
        <h1 class="mt-2 font-display text-4xl uppercase tracking-wide text-cream-50">Tienda</h1>
    </div>

    @if ($categories->isNotEmpty())
        <div class="mb-10 flex flex-wrap justify-center gap-3">
            <button
                wire:click="selectCategory(null)"
                class="rounded-full border px-5 py-2 text-sm font-semibold transition {{ is_null($category) ? 'border-gold-500 bg-gold-500 text-cumbre-950' : 'border-cumbre-700 text-cream-200 hover:border-gold-500' }}"
            >
                Todas
            </button>
            @foreach ($categories as $cat)
                <button
                    wire:click="selectCategory({{ $cat->id }})"
                    class="rounded-full border px-5 py-2 text-sm font-semibold transition {{ $category === $cat->id ? 'border-gold-500 bg-gold-500 text-cumbre-950' : 'border-cumbre-700 text-cream-200 hover:border-gold-500' }}"
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
            <div class="group overflow-hidden rounded-xl border border-cumbre-700 bg-cumbre-900">
                <div class="relative aspect-square overflow-hidden">
                    <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                    @if ($product->is_popular)
                        <span class="absolute left-3 top-3 rounded-full bg-gold-500 px-3 py-1 text-xs font-semibold text-cumbre-950">Popular</span>
                    @endif
                    @if ($product->discount_percent)
                        <span class="absolute right-3 top-3 rounded-full bg-red-500 px-3 py-1 text-xs font-semibold text-cream-50">-{{ $product->discount_percent }}%</span>
                    @endif
                    @if ($outOfStock)
                        <span class="absolute inset-x-3 bottom-3 rounded-full bg-cumbre-950/90 px-3 py-1 text-center text-xs font-semibold uppercase tracking-wide text-cream-100">Agotado</span>
                    @endif
                </div>
                <div class="p-5">
                    <p class="text-xs uppercase tracking-wide text-gold-400">{{ $product->style }} @if($product->volume_ml) · {{ $product->volume_ml }} ml @endif</p>
                    <h3 class="mt-1 font-display text-xl text-cream-50">{{ $product->name }}</h3>
                    @if ($product->description)
                        <p class="mt-2 line-clamp-2 text-sm text-cream-200/70">{{ $product->description }}</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between gap-3" @if($product->has_variants) x-data="{ variantId: {{ $activeVariants->first()->id ?? 'null' }} }" @endif>
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-cream-100">S/ {{ number_format($product->price, 2) }}</p>
                            @if ($product->compare_at_price)
                                <p class="text-sm text-cream-200/50 line-through">S/ {{ number_format($product->compare_at_price, 2) }}</p>
                            @endif
                        </div>
                        @if ($product->has_variants)
                            <div class="flex items-center gap-2">
                                <select x-model.number="variantId" class="rounded-lg border border-cumbre-700 bg-cumbre-950 px-2 py-1 text-xs text-cream-100 focus:border-gold-500 focus:outline-none">
                                    @foreach ($activeVariants as $variant)
                                        <option value="{{ $variant->id }}" @if ($variant->stock <= 0) disabled @endif>
                                            {{ $variant->label }}@if ($variant->stock <= 0) (agotado)@endif
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" @click="$wire.addToCart('product', {{ $product->id }}, variantId)" class="rounded-lg bg-gold-500 px-3 py-1.5 text-xs font-semibold text-cumbre-950 hover:bg-gold-400">
                                    Agregar
                                </button>
                            </div>
                        @elseif ($product->stock > 0)
                            <button wire:click="addToCart('product', {{ $product->id }})" class="rounded-lg bg-gold-500 px-3 py-1.5 text-xs font-semibold text-cumbre-950 hover:bg-gold-400">
                                Agregar
                            </button>
                        @else
                            <button type="button" disabled class="cursor-not-allowed rounded-lg bg-cumbre-800 px-3 py-1.5 text-xs font-semibold text-cream-100/50">
                                Agotado
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-cream-200/60">No hay productos en esta línea todavía.</p>
        @endforelse
    </div>

    @if ($packs->isNotEmpty())
        <div class="mt-16">
            <h2 class="mb-8 text-center font-display text-3xl uppercase tracking-wide text-cream-50">Packs</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($packs as $pack)
                    <div class="overflow-hidden rounded-xl border border-cumbre-700 bg-cumbre-900">
                        <div class="relative aspect-square overflow-hidden">
                            <img src="{{ $pack->cover_url }}" alt="{{ $pack->name }}" class="h-full w-full object-cover">
                            @if ($pack->discount_percent)
                                <span class="absolute right-3 top-3 rounded-full bg-red-500 px-3 py-1 text-xs font-semibold text-cream-50">-{{ $pack->discount_percent }}%</span>
                            @endif
                            @if ($pack->is_out_of_stock)
                                <span class="absolute inset-x-3 bottom-3 rounded-full bg-cumbre-950/90 px-3 py-1 text-center text-xs font-semibold uppercase tracking-wide text-cream-100">Agotado</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-display text-xl text-cream-50">{{ $pack->name }}</h3>
                            @if ($pack->description)
                                <p class="mt-2 line-clamp-2 text-sm text-cream-200/70">{{ $pack->description }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <p class="font-semibold text-cream-100">S/ {{ number_format($pack->price, 2) }}</p>
                                    @if ($pack->compare_at_price)
                                        <p class="text-sm text-cream-200/50 line-through">S/ {{ number_format($pack->compare_at_price, 2) }}</p>
                                    @endif
                                </div>
                                @if (! $pack->is_out_of_stock)
                                    <button wire:click="addToCart('pack', {{ $pack->id }})" class="rounded-lg bg-gold-500 px-3 py-1.5 text-xs font-semibold text-cumbre-950 hover:bg-gold-400">
                                        Agregar
                                    </button>
                                @else
                                    <button type="button" disabled class="cursor-not-allowed rounded-lg bg-cumbre-800 px-3 py-1.5 text-xs font-semibold text-cream-100/50">
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
            <h2 class="mb-8 text-center font-display text-3xl uppercase tracking-wide text-cream-50">Arma tu pack</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($packTemplates as $template)
                    <a href="{{ route('pack-builder.show', $template) }}" class="overflow-hidden rounded-xl border border-cumbre-700 bg-cumbre-900 p-5 transition hover:border-gold-500">
                        <h3 class="font-display text-xl text-cream-50">{{ $template->name }}</h3>
                        @if ($template->description)
                            <p class="mt-2 line-clamp-2 text-sm text-cream-200/70">{{ $template->description }}</p>
                        @endif
                        <p class="mt-4 text-sm text-gold-400">{{ $template->bottle_count }} unidades · arma tu selección</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
