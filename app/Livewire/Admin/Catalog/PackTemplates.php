<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\PackTemplate;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class PackTemplates extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $type = PackTemplate::TYPE_MIX;

    public string $bottle_count = '';

    public string $base_price = '';

    public ?int $included_merch_product_id = null;

    public string $delivery_cost = '';

    public bool $free_shipping_eligible = true;

    public string $delivery_note = '';

    public string $description = '';

    public bool $is_active = true;

    public int $sort_order = 0;

    /** @var array<int, int> */
    public array $eligible_product_ids = [];

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $template = PackTemplate::with('eligibleProducts')->findOrFail($id);

        $this->editingId = $template->id;
        $this->name = $template->name;
        $this->type = $template->type;
        $this->bottle_count = (string) $template->bottle_count;
        $this->base_price = (string) $template->base_price;
        $this->included_merch_product_id = $template->included_merch_product_id;
        $this->delivery_cost = (string) $template->delivery_cost;
        $this->free_shipping_eligible = $template->free_shipping_eligible;
        $this->delivery_note = (string) $template->delivery_note;
        $this->description = (string) $template->description;
        $this->is_active = $template->is_active;
        $this->sort_order = $template->sort_order;
        $this->eligible_product_ids = $template->eligibleProducts->pluck('id')->all();

        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:'.implode(',', [PackTemplate::TYPE_FIXED_STYLE, PackTemplate::TYPE_MIX, PackTemplate::TYPE_GIFT]),
            'bottle_count' => 'required|integer|min:1',
            'base_price' => 'nullable|numeric|min:0',
            'included_merch_product_id' => 'nullable|exists:products,id',
            'delivery_cost' => 'nullable|numeric|min:0',
            'free_shipping_eligible' => 'boolean',
            'delivery_note' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'eligible_product_ids' => 'array',
            'eligible_product_ids.*' => 'exists:products,id',
        ]);

        $eligibleIds = $data['eligible_product_ids'];
        unset($data['eligible_product_ids']);

        $data['base_price'] = $data['base_price'] !== '' ? $data['base_price'] : null;
        $data['delivery_cost'] = $data['delivery_cost'] !== '' ? $data['delivery_cost'] : null;
        $data['delivery_note'] = $data['delivery_note'] ?: null;
        $data['included_merch_product_id'] = $data['included_merch_product_id'] ?: null;

        if ($this->editingId) {
            $template = PackTemplate::findOrFail($this->editingId);
            $template->update($data);
        } else {
            $data['slug'] = Str::slug($this->name).'-'.Str::random(4);
            $template = PackTemplate::create($data);
        }

        $template->eligibleProducts()->sync($eligibleIds);

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        PackTemplate::findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'name', 'bottle_count', 'base_price', 'included_merch_product_id',
            'delivery_cost', 'delivery_note', 'description', 'sort_order', 'eligible_product_ids',
        ]);
        $this->type = PackTemplate::TYPE_MIX;
        $this->free_shipping_eligible = true;
        $this->is_active = true;
        $this->sort_order = 0;
    }

    public function render()
    {
        return view('livewire.admin.catalog.pack-templates', [
            'title' => 'Armador de packs',
            'templates' => PackTemplate::with('eligibleProducts', 'includedMerchProduct')->orderBy('sort_order')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
