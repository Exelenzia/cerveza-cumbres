@php($data = $block->data)
@if (! empty($data['items']))
    <section class="bg-cumbre-950 py-20">
        <div class="mx-auto max-w-5xl px-6">
            @if (! empty($data['heading']))
                <h2 class="mb-10 text-center font-display text-3xl uppercase tracking-wide text-cream-50">{{ $data['heading'] }}</h2>
            @endif
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach ($data['items'] as $item)
                    @if (! empty($item['quote']))
                        <blockquote class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
                            <p class="text-cream-200/80">&ldquo;{{ $item['quote'] }}&rdquo;</p>
                            @if (! empty($item['author']))
                                <footer class="mt-4 text-sm font-semibold uppercase tracking-wide text-gold-400">{{ $item['author'] }}</footer>
                            @endif
                        </blockquote>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
