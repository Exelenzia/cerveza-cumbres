<div>
    <section class="relative overflow-hidden border-b-[3px] border-text bg-surface">
        <div class="mx-auto flex max-w-7xl flex-col items-start px-6 py-24 sm:py-32">
            <span class="badge-brutal mb-6 bg-secondary px-3 py-1 text-sm font-bold uppercase tracking-[0.3em] text-surface">Cerveza artesanal &middot; Perú</span>
            <h1 class="text-brutal-hero font-display uppercase text-text" data-reveal>
                Cumbres
            </h1>
            <p class="mt-6 max-w-xl font-serif text-lg italic text-text/80" data-reveal>
                "Cerveza artesanal nacida en las alturas, para los que saben."
            </p>
            <div class="mt-10 flex flex-wrap gap-4" data-reveal>
                <a href="{{ route('shop') }}" class="btn-brutal bg-primary px-8 py-3 font-display font-bold uppercase tracking-wide text-primary-on">
                    Ver tienda
                </a>
                <a href="#historia" class="btn-brutal bg-surface-elevated px-8 py-3 font-display font-bold uppercase tracking-wide text-text">
                    Nuestra historia
                </a>
            </div>
        </div>
    </section>

    @if ($popularProducts->isNotEmpty())
        <section class="bg-surface-muted py-20">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="mb-10 font-display text-3xl uppercase tracking-wide text-text" data-reveal>Las más pedidas</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($popularProducts as $product)
                        @php
                            $activeVariants = $product->variants->where('is_active', true);
                            $outOfStock = $product->has_variants ? $activeVariants->sum('stock') <= 0 : $product->stock <= 0;
                        @endphp
                        <div class="card-brutal-interactive group overflow-hidden" data-reveal>
                            <div class="relative aspect-square overflow-hidden border-b-[3px] border-text">
                                <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                                <span class="badge-brutal absolute left-3 top-3 bg-primary px-3 py-1 text-xs font-bold text-primary-on">Popular</span>
                                @if ($product->discount_percent)
                                    <span class="badge-brutal absolute right-3 top-3 bg-red-500 px-3 py-1 text-xs font-bold text-white">-{{ $product->discount_percent }}%</span>
                                @endif
                                @if ($outOfStock)
                                    <span class="absolute inset-x-3 bottom-3 border-[2px] border-text bg-surface/90 px-3 py-1 text-center text-xs font-bold uppercase tracking-wide text-text">Agotado</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-xs uppercase tracking-wide text-secondary">{{ $product->style }}</p>
                                <h3 class="mt-1 font-display text-lg text-text">{{ $product->name }}</h3>
                                <div class="mt-2 flex items-center justify-between gap-2" @if($product->has_variants) x-data="{ variantId: {{ $activeVariants->first()->id ?? 'null' }} }" @endif>
                                    <p class="font-semibold text-text">S/ {{ number_format($product->price, 2) }}</p>
                                    @if ($product->has_variants)
                                        <div class="flex items-center gap-2">
                                            <select x-model.number="variantId" class="border-[2px] border-text bg-surface px-2 py-1 text-xs text-text focus:outline-none">
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
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($categories->isNotEmpty())
        <section id="lineas" class="bg-surface py-20">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="mb-2 font-display text-3xl uppercase tracking-wide text-text" data-reveal>Líneas cerveceras</h2>
                <p class="mb-10 max-w-xl text-sm text-text/60" data-reveal>Cada línea es un caso de estudio propio: un origen, una receta, un carácter.</p>
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('shop', ['category' => $category->id]) }}"
                            class="card-brutal-interactive block p-6 {{ $loop->iteration % 2 === 0 ? '-rotate-1' : 'rotate-1' }}"
                            data-reveal
                        >
                            <h3 class="font-display text-2xl uppercase tracking-wide text-text">{{ $category->name }}</h3>
                            <p class="mt-2 text-sm text-text/70">{{ $category->description }}</p>
                            <p class="mt-4 font-display text-sm font-bold uppercase tracking-wide text-secondary">{{ $category->products_count }} {{ \Illuminate\Support\Str::plural('cerveza', $category->products_count) }} &rarr;</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($teamBlocks->isNotEmpty())
        <section id="equipo" class="border-y-[3px] border-text bg-surface-muted py-20">
            <div class="mx-auto max-w-7xl px-6">
                @foreach ($teamBlocks as $block)
                    @if (filled($block->data['heading'] ?? null))
                        <h2 class="mb-10 font-display text-3xl uppercase tracking-wide text-text" data-reveal>{{ $block->data['heading'] }}</h2>
                    @else
                        <h2 class="mb-10 font-display text-3xl uppercase tracking-wide text-text" data-reveal>Quiénes hacemos Cumbres</h2>
                    @endif
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach (($block->data['items'] ?? []) as $j => $member)
                            <div class="card-brutal p-5 text-center" data-reveal>
                                @if ($block->teamPhotoUrl($j))
                                    <img src="{{ $block->teamPhotoUrl($j) }}" alt="{{ $member['name'] }}" class="mx-auto mb-4 h-28 w-28 border-[3px] border-text object-cover">
                                @else
                                    <div class="mx-auto mb-4 flex h-28 w-28 items-center justify-center border-[3px] border-text bg-surface-elevated font-display text-3xl text-text/40">
                                        {{ \Illuminate\Support\Str::substr($member['name'] ?? '?', 0, 1) }}
                                    </div>
                                @endif
                                <p class="font-display text-lg uppercase tracking-wide text-text">{{ $member['name'] }}</p>
                                <p class="mt-1 text-sm text-secondary">{{ $member['role'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section id="historia" class="bg-surface py-20">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <h2 class="mb-6 font-display text-3xl uppercase tracking-wide text-text" data-reveal>Nuestra historia</h2>
            <p class="text-text/80" data-reveal>
                Cumbres nació entre montañas, con la idea de llevar el sabor artesanal peruano a cada mesa. Elaboramos
                cada lote con ingredientes seleccionados y procesos tradicionales, honrando el paisaje que nos inspira.
            </p>
            <p class="mt-4 font-serif text-secondary italic" data-reveal>
                Desde Lima, con ingredientes cuidadosamente seleccionados de todo el Perú.
            </p>
        </div>
    </section>

    @php($waPhone = \App\Models\WhatsappSetting::current()?->wa_link_phone)
    @if ($waPhone)
        <section id="contacto" class="border-t-[3px] border-text bg-surface-muted py-20">
            <div class="mx-auto max-w-xl px-6">
                <h2 class="mb-2 text-center font-display text-3xl uppercase tracking-wide text-text" data-reveal>Escríbenos</h2>
                <p class="mb-8 text-center text-sm text-text/60" data-reveal>Te respondemos por WhatsApp.</p>
                <form
                    class="card-brutal space-y-4 p-6"
                    data-reveal
                    x-data="{ name: '', message: '' }"
                    @submit.prevent="window.open('https://wa.me/{{ $waPhone }}?text=' + encodeURIComponent('Hola Cumbres, soy ' + (name || 'un cliente') + '. ' + message), '_blank')"
                >
                    <div>
                        <label class="mb-1 block text-sm font-bold uppercase tracking-wide text-text-muted">Nombre</label>
                        <input type="text" x-model="name" required class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-bold uppercase tracking-wide text-text-muted">Mensaje</label>
                        <textarea x-model="message" rows="4" required class="w-full border-[2px] border-text bg-surface px-3 py-2 text-text focus:outline-none"></textarea>
                    </div>
                    <button type="submit" class="btn-brutal w-full bg-primary px-6 py-3 font-display font-bold uppercase tracking-wide text-primary-on">
                        Enviar por WhatsApp
                    </button>
                </form>
            </div>
        </section>
    @endif
</div>
