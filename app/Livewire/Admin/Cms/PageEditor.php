<?php

namespace App\Livewire\Admin\Cms;

use App\Models\Category;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class PageEditor extends Component
{
    use WithFileUploads;

    private const RESERVED_SLUGS = [
        'tienda', 'carrito', 'checkout', 'pedidos', 'mis-pedidos', 'login', 'registro', 'admin', 'paginas',
    ];

    public Page $page;

    public string $title = '';

    public string $slug = '';

    public bool $is_active = true;

    public array $blocks = [];

    public array $newImages = [];

    public string $addType = PageBlock::TYPE_HERO;

    public ?int $savedBlockId = null;

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->is_active = $page->is_active;
        $this->loadBlocks();
    }

    public function savePageMeta(): void
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('pages', 'slug')->ignore($this->page->id),
                Rule::notIn(self::RESERVED_SLUGS),
            ],
            'is_active' => 'boolean',
        ]);

        $this->page->update($data);
    }

    public function addBlock(): void
    {
        if (! in_array($this->addType, PageBlock::TYPES, true)) {
            return;
        }

        PageBlock::create([
            'page_id' => $this->page->id,
            'type' => $this->addType,
            'data' => PageBlock::defaultData($this->addType),
            'sort_order' => (int) $this->page->blocks()->max('sort_order') + 1,
        ]);

        $this->loadBlocks();
    }

    public function removeBlock(int $blockId): void
    {
        PageBlock::where('page_id', $this->page->id)->findOrFail($blockId)->delete();
        $this->loadBlocks();
    }

    public function moveBlock(int $blockId, string $direction): void
    {
        $blocks = $this->page->blocks()->get();
        $index = $blocks->search(fn (PageBlock $b) => $b->id === $blockId);

        if ($index === false) {
            return;
        }

        $swapWith = $direction === 'up' ? $index - 1 : $index + 1;

        if (! isset($blocks[$swapWith])) {
            return;
        }

        $current = $blocks[$index];
        $other = $blocks[$swapWith];

        [$currentOrder, $otherOrder] = [$current->sort_order, $other->sort_order];
        $current->update(['sort_order' => $otherOrder]);
        $other->update(['sort_order' => $currentOrder]);

        $this->loadBlocks();
    }

    public function addTestimonial(int $blockId): void
    {
        $idx = $this->blockIndex($blockId);

        if ($idx === null) {
            return;
        }

        $this->blocks[$idx]['data']['items'][] = ['author' => '', 'quote' => ''];
    }

    public function removeTestimonial(int $blockId, int $itemIndex): void
    {
        $idx = $this->blockIndex($blockId);

        if ($idx === null) {
            return;
        }

        unset($this->blocks[$idx]['data']['items'][$itemIndex]);
        $this->blocks[$idx]['data']['items'] = array_values($this->blocks[$idx]['data']['items']);
    }

    public function saveBlockData(int $blockId): void
    {
        $this->savedBlockId = null;
        $idx = $this->blockIndex($blockId);

        if ($idx === null) {
            return;
        }

        $type = $this->blocks[$idx]['type'];
        $rules = $this->rulesFor($type, $idx);

        if (isset($this->newImages[$blockId])) {
            $rules["newImages.{$blockId}"] = 'nullable|image|max:4096';
        }

        $this->validate($rules);

        $block = PageBlock::where('page_id', $this->page->id)->findOrFail($blockId);
        $block->update(['data' => $this->blocks[$idx]['data']]);

        if (! empty($this->newImages[$blockId])) {
            $block->addMedia($this->newImages[$blockId]->getRealPath())
                ->usingFileName($this->newImages[$blockId]->getClientOriginalName())
                ->toMediaCollection('image');

            unset($this->newImages[$blockId]);
        }

        $this->savedBlockId = $blockId;
        $this->loadBlocks();
    }

    private function blockIndex(int $blockId): ?int
    {
        foreach ($this->blocks as $i => $block) {
            if ($block['id'] === $blockId) {
                return $i;
            }
        }

        return null;
    }

    private function rulesFor(string $type, int $idx): array
    {
        $prefix = "blocks.{$idx}.data.";

        return match ($type) {
            PageBlock::TYPE_HERO => [
                $prefix.'heading' => 'required|string|max:255',
                $prefix.'subheading' => 'nullable|string|max:500',
                $prefix.'button_label' => 'nullable|string|max:100',
                $prefix.'button_link' => 'nullable|string|max:255',
            ],
            PageBlock::TYPE_TEXT_IMAGE => [
                $prefix.'heading' => 'required|string|max:255',
                $prefix.'body' => 'required|string',
                $prefix.'image_position' => 'required|in:left,right',
            ],
            PageBlock::TYPE_PRODUCT_GRID => [
                $prefix.'heading' => 'nullable|string|max:255',
                $prefix.'source' => 'required|in:popular,category',
                $prefix.'category_id' => 'nullable|exists:categories,id',
                $prefix.'limit' => 'required|integer|min:1|max:12',
            ],
            PageBlock::TYPE_TESTIMONIALS => [
                $prefix.'heading' => 'nullable|string|max:255',
                $prefix.'items' => 'required|array|min:1',
                $prefix.'items.*.author' => 'required|string|max:255',
                $prefix.'items.*.quote' => 'required|string|max:1000',
            ],
            PageBlock::TYPE_CTA => [
                $prefix.'heading' => 'required|string|max:255',
                $prefix.'body' => 'nullable|string|max:1000',
                $prefix.'button_label' => 'required|string|max:100',
                $prefix.'button_link' => 'required|string|max:255',
            ],
            default => [],
        };
    }

    private function loadBlocks(): void
    {
        $this->blocks = $this->page->blocks()->get()->map(fn (PageBlock $block) => [
            'id' => $block->id,
            'type' => $block->type,
            'data' => $block->data,
            'image_url' => $block->image_url,
        ])->all();
    }

    public function render()
    {
        return view('livewire.admin.cms.page-editor', [
            'title' => 'Editar: '.$this->page->title,
            'blockTypes' => PageBlock::TYPES,
            'blockLabels' => PageBlock::LABELS,
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }
}
