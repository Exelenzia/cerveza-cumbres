@php($data = $block->data)
<section class="bg-gold-500 py-16">
    <div class="mx-auto max-w-3xl px-6 text-center">
        <h2 class="mb-4 font-display text-3xl uppercase tracking-wide text-cumbre-950">{{ $data['heading'] ?? '' }}</h2>
        @if (! empty($data['body']))
            <p class="mb-8 text-cumbre-900/80">{{ $data['body'] }}</p>
        @endif
        @if (! empty($data['button_label']) && ! empty($data['button_link']))
            <a href="{{ $data['button_link'] }}" class="inline-block rounded-lg bg-cumbre-950 px-8 py-3 font-semibold text-gold-400 hover:bg-cumbre-900">
                {{ $data['button_label'] }}
            </a>
        @endif
    </div>
</section>
