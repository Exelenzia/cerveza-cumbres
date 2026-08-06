@php($data = $block->data)
<section class="bg-primary py-16">
    <div class="mx-auto max-w-3xl px-6 text-center">
        <h2 class="mb-4 font-display text-3xl uppercase tracking-wide text-primary-on">{{ $data['heading'] ?? '' }}</h2>
        @if (! empty($data['body']))
            <p class="mb-8 text-primary-on/80">{{ $data['body'] }}</p>
        @endif
        @if (! empty($data['button_label']) && ! empty($data['button_link']))
            <a href="{{ $data['button_link'] }}" class="inline-block rounded-lg bg-surface px-8 py-3 font-semibold text-primary hover:bg-surface-muted">
                {{ $data['button_label'] }}
            </a>
        @endif
    </div>
</section>
