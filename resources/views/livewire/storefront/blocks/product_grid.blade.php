@php($data = $block->data)
@if ($products->isNotEmpty())
    <section class="bg-cumbre-900 py-20">
        <div class="mx-auto max-w-7xl px-6">
            @if (! empty($data['heading']))
                <h2 class="mb-10 text-center font-display text-3xl uppercase tracking-wide text-cream-50">{{ $data['heading'] }}</h2>
            @endif
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <div class="group overflow-hidden rounded-xl border border-cumbre-700 bg-cumbre-950">
                        <div class="relative aspect-square overflow-hidden">
                            <img src="{{ $product->cover_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
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
