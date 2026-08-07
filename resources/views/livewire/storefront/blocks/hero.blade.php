@php($data = $block->data)
<section class="relative overflow-hidden bg-surface">
    <div class="mx-auto flex max-w-7xl flex-col items-center px-6 py-24 text-center sm:py-32">
        @if ($block->image_url)
            <img src="{{ $block->image_url }}" alt="" class="mb-8 max-h-64 border-[3px] border-text object-cover">
        @endif
        <h1 class="font-display text-4xl uppercase tracking-wide text-text sm:text-6xl" data-reveal>
            {{ $data['heading'] ?? '' }}
        </h1>
        @if (! empty($data['subheading']))
            <p class="mt-6 max-w-xl text-lg text-text/80" data-reveal>{{ $data['subheading'] }}</p>
        @endif
        @if (! empty($data['button_label']) && ! empty($data['button_link']))
            <a href="{{ $data['button_link'] }}" class="btn-brutal mt-10 bg-primary px-8 py-3 font-semibold text-primary-on hover:bg-primary-hover" data-reveal>
                {{ $data['button_label'] }}
            </a>
        @endif
    </div>
</section>
