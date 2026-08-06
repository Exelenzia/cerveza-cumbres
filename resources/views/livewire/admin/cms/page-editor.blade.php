<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.cms.pages.index') }}" wire:navigate class="text-sm text-cream-200/70 hover:text-gold-400">&larr; Volver a páginas</a>
        @if ($page->is_active)
            <a href="{{ route('pages.show', $page) }}" target="_blank" class="text-sm text-gold-400 hover:text-gold-300">Ver página &rarr;</a>
        @endif
    </div>

    <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
        <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">Datos de la página</h3>
        <form wire:submit="savePageMeta" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm text-cream-200">Título</label>
                    <input type="text" wire:model="title" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    @error('title') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm text-cream-200">Slug (URL: /paginas/...)</label>
                    <input type="text" wire:model="slug" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                    @error('slug') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-cream-200">
                <input type="checkbox" wire:model="is_active" class="rounded border-cumbre-700 bg-cumbre-950">
                Página activa (visible en el storefront)
            </label>
            <button type="submit" class="rounded-lg bg-gold-500 px-4 py-2 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">Guardar</button>
        </form>
    </div>

    <div class="space-y-4">
        <h3 class="font-display text-lg uppercase tracking-wide text-cream-50">Bloques</h3>

        @forelse ($blocks as $i => $block)
            <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6" wire:key="block-{{ $block['id'] }}">
                <div class="mb-4 flex items-center justify-between">
                    <span class="rounded-full bg-cumbre-800 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gold-400">
                        {{ $blockLabels[$block['type']] ?? $block['type'] }}
                    </span>
                    <div class="flex items-center gap-3 text-sm">
                        @if ($i > 0)
                            <button type="button" wire:click="moveBlock({{ $block['id'] }}, 'up')" class="text-cream-200 hover:text-gold-400">↑ Subir</button>
                        @endif
                        @if ($i < count($blocks) - 1)
                            <button type="button" wire:click="moveBlock({{ $block['id'] }}, 'down')" class="text-cream-200 hover:text-gold-400">↓ Bajar</button>
                        @endif
                        <button type="button" wire:click="removeBlock({{ $block['id'] }})" wire:confirm="¿Eliminar este bloque?" class="text-red-400 hover:text-red-300">Eliminar</button>
                    </div>
                </div>

                @if ($savedBlockId === $block['id'])
                    <div class="mb-4 rounded-lg bg-green-500/10 px-4 py-2 text-sm text-green-400">Bloque guardado.</div>
                @endif

                <div class="space-y-4">
                    @if ($block['type'] === 'hero')
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Título</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                            @error("blocks.{$i}.data.heading") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Subtítulo</label>
                            <textarea wire:model="blocks.{{ $i }}.data.subheading" rows="2" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm text-cream-200">Texto del botón</label>
                                <input type="text" wire:model="blocks.{{ $i }}.data.button_label" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm text-cream-200">Enlace del botón</label>
                                <input type="text" wire:model="blocks.{{ $i }}.data.button_link" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                            </div>
                        </div>
                        @include('livewire.admin.cms.partials.image-field', ['i' => $i, 'block' => $block])
                    @elseif ($block['type'] === 'text_image')
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Título</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                            @error("blocks.{$i}.data.heading") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Texto</label>
                            <textarea wire:model="blocks.{{ $i }}.data.body" rows="4" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none"></textarea>
                            @error("blocks.{$i}.data.body") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Posición de la imagen</label>
                            <select wire:model="blocks.{{ $i }}.data.image_position" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                                <option value="left">Izquierda</option>
                                <option value="right">Derecha</option>
                            </select>
                        </div>
                        @include('livewire.admin.cms.partials.image-field', ['i' => $i, 'block' => $block])
                    @elseif ($block['type'] === 'product_grid')
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Título (opcional)</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-sm text-cream-200">Fuente</label>
                                <select wire:model="blocks.{{ $i }}.data.source" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                                    <option value="popular">Productos populares</option>
                                    <option value="category">Por categoría</option>
                                </select>
                            </div>
                            @if (($block['data']['source'] ?? 'popular') === 'category')
                                <div>
                                    <label class="mb-1 block text-sm text-cream-200">Categoría</label>
                                    <select wire:model="blocks.{{ $i }}.data.category_id" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                                        <option value="">Selecciona...</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div>
                                <label class="mb-1 block text-sm text-cream-200">Cantidad</label>
                                <input type="number" min="1" max="12" wire:model="blocks.{{ $i }}.data.limit" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                            </div>
                        </div>
                    @elseif ($block['type'] === 'testimonials')
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Título (opcional)</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                        </div>
                        <div class="space-y-3">
                            @foreach (($block['data']['items'] ?? []) as $j => $item)
                                <div class="rounded-lg border border-cumbre-800 p-4" wire:key="testimonial-{{ $block['id'] }}-{{ $j }}">
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div class="sm:col-span-1">
                                            <label class="mb-1 block text-sm text-cream-200">Autor</label>
                                            <input type="text" wire:model="blocks.{{ $i }}.data.items.{{ $j }}.author" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="mb-1 block text-sm text-cream-200">Testimonio</label>
                                            <textarea wire:model="blocks.{{ $i }}.data.items.{{ $j }}.quote" rows="2" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none"></textarea>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeTestimonial({{ $block['id'] }}, {{ $j }})" class="mt-2 text-sm text-red-400 hover:text-red-300">Quitar testimonio</button>
                                </div>
                            @endforeach
                        </div>
                        @error("blocks.{$i}.data.items") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        <button type="button" wire:click="addTestimonial({{ $block['id'] }})" class="rounded-lg border border-cumbre-700 px-4 py-2 text-sm text-cream-200 hover:border-gold-500">+ Agregar testimonio</button>
                    @elseif ($block['type'] === 'cta')
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Título</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                            @error("blocks.{$i}.data.heading") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-cream-200">Texto</label>
                            <textarea wire:model="blocks.{{ $i }}.data.body" rows="2" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm text-cream-200">Texto del botón</label>
                                <input type="text" wire:model="blocks.{{ $i }}.data.button_label" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                                @error("blocks.{$i}.data.button_label") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm text-cream-200">Enlace del botón</label>
                                <input type="text" wire:model="blocks.{{ $i }}.data.button_link" class="w-full rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                                @error("blocks.{$i}.data.button_link") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" wire:click="saveBlockData({{ $block['id'] }})" class="mt-4 rounded-lg bg-gold-500 px-4 py-2 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">
                    Guardar bloque
                </button>
            </div>
        @empty
            <p class="text-cream-200/60">Aún no hay bloques. Agrega uno abajo.</p>
        @endforelse
    </div>

    <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
        <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">Agregar bloque</h3>
        <div class="flex items-center gap-3">
            <select wire:model="addType" class="rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-cream-50 focus:border-gold-500 focus:outline-none">
                @foreach ($blockTypes as $type)
                    <option value="{{ $type }}">{{ $blockLabels[$type] }}</option>
                @endforeach
            </select>
            <button type="button" wire:click="addBlock" class="rounded-lg bg-gold-500 px-4 py-2 text-sm font-semibold text-cumbre-950 hover:bg-gold-400">+ Agregar</button>
        </div>
    </div>
</div>
