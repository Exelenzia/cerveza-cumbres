<div>
    <section class="relative overflow-hidden bg-cumbre-950">
        <div class="mx-auto flex max-w-7xl flex-col items-center px-6 py-24 text-center sm:py-32">
            <span class="mb-4 text-sm uppercase tracking-[0.3em] text-gold-400">Cerveza artesanal · Perú</span>
            <h1 class="font-display text-5xl uppercase tracking-wide text-cream-50 sm:text-7xl">
                Cumbres
            </h1>
            <p class="mt-6 max-w-xl text-lg text-cream-200/80">
                Cerveza artesanal nacida en las alturas. Cada línea cuenta la historia de un lugar, un sabor y una montaña.
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ route('shop') }}" class="rounded-lg bg-gold-500 px-8 py-3 font-semibold text-cumbre-950 hover:bg-gold-400">
                    Ver tienda
                </a>
                <a href="#historia" class="rounded-lg border border-gold-500 px-8 py-3 font-semibold text-gold-400 hover:bg-gold-500/10">
                    Nuestra historia
                </a>
            </div>
        </div>
    </section>

    @if ($popularProducts->isNotEmpty())
        <section class="bg-cumbre-900 py-20">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="mb-10 text-center font-display text-3xl uppercase tracking-wide text-cream-50">Las más pedidas</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($popularProducts as $product)
                        <div class="group overflow-hidden rounded-xl border border-cumbre-700 bg-cumbre-950">
                            <div class="relative aspect-square overflow-hidden">
                                <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                                <span class="absolute left-3 top-3 rounded-full bg-gold-500 px-3 py-1 text-xs font-semibold text-cumbre-950">Popular</span>
                                @if ($product->discount_percent)
                                    <span class="absolute right-3 top-3 rounded-full bg-red-500 px-3 py-1 text-xs font-semibold text-cream-50">-{{ $product->discount_percent }}%</span>
                                @endif
                                @if ($product->stock <= 0)
                                    <span class="absolute inset-x-3 bottom-3 rounded-full bg-cumbre-950/90 px-3 py-1 text-center text-xs font-semibold uppercase tracking-wide text-cream-100">Agotado</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-xs uppercase tracking-wide text-gold-400">{{ $product->style }}</p>
                                <h3 class="mt-1 font-display text-lg text-cream-50">{{ $product->name }}</h3>
                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <p class="font-semibold text-cream-100">S/ {{ number_format($product->price, 2) }}</p>
                                    @if ($product->stock > 0)
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
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($categories->isNotEmpty())
        <section id="lineas" class="bg-cumbre-950 py-20">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="mb-10 text-center font-display text-3xl uppercase tracking-wide text-cream-50">Líneas cerveceras</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop', ['category' => $category->id]) }}" class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6 transition hover:border-gold-500">
                            <h3 class="font-display text-xl uppercase tracking-wide text-cream-50">{{ $category->name }}</h3>
                            <p class="mt-2 text-sm text-cream-200/70">{{ $category->description }}</p>
                            <p class="mt-4 text-sm text-gold-400">{{ $category->products_count }} {{ \Illuminate\Support\Str::plural('cerveza', $category->products_count) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="historia" class="bg-cumbre-900 py-20">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <h2 class="mb-6 font-display text-3xl uppercase tracking-wide text-cream-50">Nuestra historia</h2>
            <p class="text-cream-200/80">
                Cumbres nació entre montañas, con la idea de llevar el sabor artesanal peruano a cada mesa. Elaboramos
                cada lote con ingredientes seleccionados y procesos tradicionales, honrando el paisaje que nos inspira.
            </p>
        </div>
    </section>
</div>
