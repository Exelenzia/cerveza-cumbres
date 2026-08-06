@php($data = $block->data)
<section class="relative overflow-hidden bg-cumbre-950">
    <div class="mx-auto flex max-w-7xl flex-col items-center px-6 py-24 text-center sm:py-32">
        @if ($block->image_url)
            <img src="{{ $block->image_url }}" alt="" class="mb-8 max-h-64 rounded-xl object-cover">
        @endif
        <h1 class="font-display text-4xl uppercase tracking-wide text-cream-50 sm:text-6xl">
            {{ $data['heading'] ?? '' }}
        </h1>
        @if (! empty($data['subheading']))
            <p class="mt-6 max-w-xl text-lg text-cream-200/80">{{ $data['subheading'] }}</p>
        @endif
        @if (! empty($data['button_label']) && ! empty($data['button_link']))
            <a href="{{ $data['button_link'] }}" class="mt-10 inline-block rounded-lg bg-gold-500 px-8 py-3 font-semibold text-cumbre-950 hover:bg-gold-400">
                {{ $data['button_label'] }}
            </a>
        @endif
    </div>
</section>
