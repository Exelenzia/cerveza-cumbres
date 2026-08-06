@php($data = $block->data)
@if (! empty($data['items']))
    <section class="bg-surface py-20">
        <div class="mx-auto max-w-5xl px-6">
            @if (! empty($data['heading']))
                <h2 class="mb-10 text-center font-display text-3xl uppercase tracking-wide text-text">{{ $data['heading'] }}</h2>
            @endif
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach ($data['items'] as $item)
                    @if (! empty($item['quote']))
                        <blockquote class="rounded-xl border border-border bg-surface-muted p-6">
                            <p class="text-text/80">&ldquo;{{ $item['quote'] }}&rdquo;</p>
                            @if (! empty($item['author']))
                                <footer class="mt-4 text-sm font-semibold uppercase tracking-wide text-secondary">{{ $item['author'] }}</footer>
                            @endif
                        </blockquote>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
