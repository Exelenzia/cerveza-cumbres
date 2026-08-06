<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.cms.pages.index') }}" wire:navigate class="text-sm text-text-muted/70 hover:text-primary">&larr; Volver a páginas</a>
        @if ($page->is_active)
            <a href="{{ route('pages.show', $page) }}" target="_blank" class="text-sm text-primary hover:text-primary-hover">Ver página &rarr;</a>
        @endif
    </div>

    <div class="rounded-xl border border-border bg-surface-elevated p-6">
        <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-text">Datos de la página</h3>
        <form wire:submit="savePageMeta" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm text-text-muted">Título</label>
                    <input type="text" wire:model="title" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    @error('title') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm text-text-muted">Slug (URL: /paginas/...)</label>
                    <input type="text" wire:model="slug" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                    @error('slug') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-text-muted">
                <input type="checkbox" wire:model="is_active" class="rounded border-border bg-surface">
                Página activa (visible en el storefront)
            </label>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-on hover:bg-primary-hover">Guardar</button>
        </form>
    </div>

    <div class="space-y-4">
        <h3 class="font-display text-lg uppercase tracking-wide text-text">Bloques</h3>

        @forelse ($blocks as $i => $block)
            <div class="rounded-xl border border-border bg-surface-elevated p-6" wire:key="block-{{ $block['id'] }}">
                <div class="mb-4 flex items-center justify-between">
                    <span class="rounded-full bg-surface-muted px-3 py-1 text-xs font-semibold uppercase tracking-wide text-secondary">
                        {{ $blockLabels[$block['type']] ?? $block['type'] }}
                    </span>
                    <div class="flex items-center gap-3 text-sm">
                        @if ($i > 0)
                            <button type="button" wire:click="moveBlock({{ $block['id'] }}, 'up')" class="text-text-muted hover:text-primary">↑ Subir</button>
                        @endif
                        @if ($i < count($blocks) - 1)
                            <button type="button" wire:click="moveBlock({{ $block['id'] }}, 'down')" class="text-text-muted hover:text-primary">↓ Bajar</button>
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
                            <label class="mb-1 block text-sm text-text-muted">Título</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            @error("blocks.{$i}.data.heading") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Subtítulo</label>
                            <textarea wire:model="blocks.{{ $i }}.data.subheading" rows="2" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm text-text-muted">Texto del botón</label>
                                <input type="text" wire:model="blocks.{{ $i }}.data.button_label" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm text-text-muted">Enlace del botón</label>
                                <input type="text" wire:model="blocks.{{ $i }}.data.button_link" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            </div>
                        </div>
                        @include('livewire.admin.cms.partials.image-field', ['i' => $i, 'block' => $block])
                    @elseif ($block['type'] === 'text_image')
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Título</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            @error("blocks.{$i}.data.heading") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Texto</label>
                            <textarea wire:model="blocks.{{ $i }}.data.body" rows="4" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"></textarea>
                            @error("blocks.{$i}.data.body") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Posición de la imagen</label>
                            <select wire:model="blocks.{{ $i }}.data.image_position" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                <option value="left">Izquierda</option>
                                <option value="right">Derecha</option>
                            </select>
                        </div>
                        @include('livewire.admin.cms.partials.image-field', ['i' => $i, 'block' => $block])
                    @elseif ($block['type'] === 'product_grid')
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Título (opcional)</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-sm text-text-muted">Fuente</label>
                                <select wire:model="blocks.{{ $i }}.data.source" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                    <option value="popular">Productos populares</option>
                                    <option value="category">Por categoría</option>
                                </select>
                            </div>
                            @if (($block['data']['source'] ?? 'popular') === 'category')
                                <div>
                                    <label class="mb-1 block text-sm text-text-muted">Categoría</label>
                                    <select wire:model="blocks.{{ $i }}.data.category_id" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                        <option value="">Selecciona...</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div>
                                <label class="mb-1 block text-sm text-text-muted">Cantidad</label>
                                <input type="number" min="1" max="12" wire:model="blocks.{{ $i }}.data.limit" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            </div>
                        </div>
                    @elseif ($block['type'] === 'testimonials')
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Título (opcional)</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                        </div>
                        <div class="space-y-3">
                            @foreach (($block['data']['items'] ?? []) as $j => $item)
                                <div class="rounded-lg border border-border p-4" wire:key="testimonial-{{ $block['id'] }}-{{ $j }}">
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div class="sm:col-span-1">
                                            <label class="mb-1 block text-sm text-text-muted">Autor</label>
                                            <input type="text" wire:model="blocks.{{ $i }}.data.items.{{ $j }}.author" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="mb-1 block text-sm text-text-muted">Testimonio</label>
                                            <textarea wire:model="blocks.{{ $i }}.data.items.{{ $j }}.quote" rows="2" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"></textarea>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeTestimonial({{ $block['id'] }}, {{ $j }})" class="mt-2 text-sm text-red-400 hover:text-red-300">Quitar testimonio</button>
                                </div>
                            @endforeach
                        </div>
                        @error("blocks.{$i}.data.items") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        <button type="button" wire:click="addTestimonial({{ $block['id'] }})" class="rounded-lg border border-border px-4 py-2 text-sm text-text-muted hover:border-primary">+ Agregar testimonio</button>
                    @elseif ($block['type'] === 'cta')
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Título</label>
                            <input type="text" wire:model="blocks.{{ $i }}.data.heading" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                            @error("blocks.{$i}.data.heading") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-text-muted">Texto</label>
                            <textarea wire:model="blocks.{{ $i }}.data.body" rows="2" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm text-text-muted">Texto del botón</label>
                                <input type="text" wire:model="blocks.{{ $i }}.data.button_label" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                @error("blocks.{$i}.data.button_label") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm text-text-muted">Enlace del botón</label>
                                <input type="text" wire:model="blocks.{{ $i }}.data.button_link" class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                                @error("blocks.{$i}.data.button_link") <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" wire:click="saveBlockData({{ $block['id'] }})" class="mt-4 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-on hover:bg-primary-hover">
                    Guardar bloque
                </button>
            </div>
        @empty
            <p class="text-text-muted/60">Aún no hay bloques. Agrega uno abajo.</p>
        @endforelse
    </div>

    <div class="rounded-xl border border-border bg-surface-elevated p-6">
        <h3 class="mb-4 font-display text-lg uppercase tracking-wide text-text">Agregar bloque</h3>
        <div class="flex items-center gap-3">
            <select wire:model="addType" class="rounded-lg border border-border bg-surface px-3 py-2 text-text focus:border-primary focus:outline-none">
                @foreach ($blockTypes as $type)
                    <option value="{{ $type }}">{{ $blockLabels[$type] }}</option>
                @endforeach
            </select>
            <button type="button" wire:click="addBlock" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-on hover:bg-primary-hover">+ Agregar</button>
        </div>
    </div>
</div>
