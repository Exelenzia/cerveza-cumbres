<div>
    <section class="relative overflow-hidden bg-surface">
        <div class="mx-auto flex max-w-7xl flex-col items-center px-6 py-24 text-center sm:py-32">
            <span class="mb-4 text-sm uppercase tracking-[0.3em] text-secondary">Cerveza artesanal · Perú</span>
            <h1 class="font-display text-5xl uppercase tracking-wide text-text sm:text-7xl">
                Cumbres
            </h1>
            <p class="mt-6 max-w-xl text-lg text-text/80">
                Cerveza artesanal nacida en las alturas. Cada línea cuenta la historia de un lugar, un sabor y una montaña.
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ route('shop') }}" class="rounded-lg bg-primary px-8 py-3 font-semibold text-primary-on hover:bg-primary-hover">
                    Ver tienda
                </a>
                <a href="#historia" class="rounded-lg border border-primary px-8 py-3 font-semibold text-primary hover:bg-primary/10">
                    Nuestra historia
                </a>
            </div>
        </div>
    </section>

    @if ($popularProducts->isNotEmpty())
        <section class="bg-surface-muted py-20">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="mb-10 text-center font-display text-3xl uppercase tracking-wide text-text">Las más pedidas</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($popularProducts as $product)
                        @php
                            $activeVariants = $product->variants->where('is_active', true);
                            $outOfStock = $product->has_variants ? $activeVariants->sum('stock') <= 0 : $product->stock <= 0;
                        @endphp
                        <div class="group overflow-hidden rounded-xl border border-border bg-surface">
                            <div class="relative aspect-square overflow-hidden">
                                <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                                <span class="absolute left-3 top-3 rounded-full bg-primary px-3 py-1 text-xs font-semibold text-primary-on">Popular</span>
                                @if ($product->discount_percent)
                                    <span class="absolute right-3 top-3 rounded-full bg-red-500 px-3 py-1 text-xs font-semibold text-white">-{{ $product->discount_percent }}%</span>
                                @endif
                                @if ($outOfStock)
                                    <span class="absolute inset-x-3 bottom-3 rounded-full bg-surface/90 px-3 py-1 text-center text-xs font-semibold uppercase tracking-wide text-text">Agotado</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-xs uppercase tracking-wide text-secondary">{{ $product->style }}</p>
                                <h3 class="mt-1 font-display text-lg text-text">{{ $product->name }}</h3>
                                <div class="mt-2 flex items-center justify-between gap-2" @if($product->has_variants) x-data="{ variantId: {{ $activeVariants->first()->id ?? 'null' }} }" @endif>
                                    <p class="font-semibold text-text">S/ {{ number_format($product->price, 2) }}</p>
                                    @if ($product->has_variants)
                                        <div class="flex items-center gap-2">
                                            <select x-model.number="variantId" class="rounded-lg border border-border bg-surface px-2 py-1 text-xs text-text focus:border-primary focus:outline-none">
                                                @foreach ($activeVariants as $variant)
                                                    <option value="{{ $variant->id }}" @if ($variant->stock <= 0) disabled @endif>
                                                        {{ $variant->label }}@if ($variant->stock <= 0) (agotado)@endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" @click="$wire.addToCart('product', {{ $product->id }}, variantId)" class="rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-primary-on hover:bg-primary-hover">
                                                Agregar
                                            </button>
                                        </div>
                                    @elseif ($product->stock > 0)
                                        <button wire:click="addToCart('product', {{ $product->id }})" class="rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-primary-on hover:bg-primary-hover">
                                            Agregar
                                        </button>
                                    @else
                                        <button type="button" disabled class="cursor-not-allowed rounded-lg bg-surface-muted px-3 py-1.5 text-xs font-semibold text-text/50">
                                            Agotado
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($categories->isNotEmpty())
        <section id="lineas" class="bg-surface py-20">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="mb-10 text-center font-display text-3xl uppercase tracking-wide text-text">Líneas cerveceras</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop', ['category' => $category->id]) }}" class="rounded-xl border border-border bg-surface-muted p-6 transition hover:border-primary">
                            <h3 class="font-display text-xl uppercase tracking-wide text-text">{{ $category->name }}</h3>
                            <p class="mt-2 text-sm text-text/70">{{ $category->description }}</p>
                            <p class="mt-4 text-sm text-secondary">{{ $category->products_count }} {{ \Illuminate\Support\Str::plural('cerveza', $category->products_count) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="historia" class="bg-surface-muted py-20">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <h2 class="mb-6 font-display text-3xl uppercase tracking-wide text-text">Nuestra historia</h2>
            <p class="text-text/80">
                Cumbres nació entre montañas, con la idea de llevar el sabor artesanal peruano a cada mesa. Elaboramos
                cada lote con ingredientes seleccionados y procesos tradicionales, honrando el paisaje que nos inspira.
            </p>
        </div>
    </section>
</div>
