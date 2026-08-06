@php($data = $block->data)
<section class="bg-surface-muted py-20">
    <div class="mx-auto max-w-6xl px-6">
        <div class="grid grid-cols-1 items-center gap-10 md:grid-cols-2">
            @if (($data['image_position'] ?? 'left') === 'left')
                <div class="order-1">
                    @if ($block->image_url)
                        <img src="{{ $block->image_url }}" alt="" class="w-full rounded-xl object-cover">
                    @endif
                </div>
                <div class="order-2">
                    <h2 class="mb-4 font-display text-3xl uppercase tracking-wide text-text">{{ $data['heading'] ?? '' }}</h2>
                    <p class="text-text/80">{{ $data['body'] ?? '' }}</p>
                </div>
            @else
                <div class="order-2 md:order-1">
                    <h2 class="mb-4 font-display text-3xl uppercase tracking-wide text-text">{{ $data['heading'] ?? '' }}</h2>
                    <p class="text-text/80">{{ $data['body'] ?? '' }}</p>
                </div>
                <div class="order-1 md:order-2">
                    @if ($block->image_url)
                        <img src="{{ $block->image_url }}" alt="" class="w-full rounded-xl object-cover">
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
