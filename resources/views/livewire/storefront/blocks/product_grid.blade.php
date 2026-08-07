@php
    $data = $block->data;
@endphp
@if ($products->isNotEmpty())
    <section class="bg-surface-muted py-20">
        <div class="mx-auto max-w-7xl px-6">
            @if (! empty($data['heading']))
                <h2 class="mb-10 text-center font-display text-3xl uppercase tracking-wide text-text" data-reveal>{{ $data['heading'] }}</h2>
            @endif
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
                @foreach ($products as $product)
                    @php
                        $activeVariants = $product->variants->where('is_active', true);
                        $outOfStock = $product->has_variants ? $activeVariants->sum('stock') <= 0 : $product->stock <= 0;
                    @endphp
                    <div class="card-brutal-interactive group overflow-hidden">
                        <div class="relative aspect-square overflow-hidden border-b-[3px] border-text">
                            <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
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
