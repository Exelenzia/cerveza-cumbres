<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class Products extends Component
{
    use WithFileUploads;

    public bool $showForm = false;

    public ?int $editingId = null;

    public ?int $category_id = null;

    public string $name = '';

    public string $style = '';

    public string $description = '';

    public ?float $abv = null;

    public ?int $ibu = null;

    public ?int $volume_ml = null;

    public string $price = '';

    public string $compare_at_price = '';

    public int $stock = 0;

    public bool $is_popular = false;

    public bool $is_active = true;

    public int $sort_order = 0;

    public $image = null;

    public string $fixed_pack6_price = '';

    public bool $is_mix_premium = false;

    public string $mix_surcharge_per_unit = '';

    public bool $has_variants = false;

    /** @var array<int, array{label: string, price_override: string, stock: int}> */
    public array $variants = [];

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $product = Product::with('variants')->findOrFail($id);

        $this->editingId = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->style = (string) $product->style;
        $this->description = (string) $product->description;
        $this->abv = $product->abv;
        $this->ibu = $product->ibu;
        $this->volume_ml = $product->volume_ml;
        $this->price = (string) $product->price;
        $this->compare_at_price = (string) $product->compare_at_price;
        $this->stock = $product->stock;
        $this->is_popular = $product->is_popular;
        $this->is_active = $product->is_active;
        $this->sort_order = $product->sort_order;
        $this->image = null;
        $this->fixed_pack6_price = (string) $product->fixed_pack6_price;
        $this->is_mix_premium = $product->is_mix_premium;
        $this->mix_surcharge_per_unit = (string) $product->mix_surcharge_per_unit;
        $this->variants = $product->variants->map(fn (ProductVariant $variant) => [
            'label' => $variant->label,
            'price_override' => (string) $variant->price_override,
            'stock' => $variant->stock,
        ])->all();
        $this->has_variants = $product->variants->isNotEmpty();
        $this->showForm = true;
    }

    public function addVariant(): void
    {
        $this->variants[] = ['label' => '', 'price_override' => '', 'stock' => 0];
    }

    public function removeVariant(int $index): void
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function save(): void
    {
        $data = $this->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'style' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'abv' => 'nullable|numeric|min:0|max:99.9',
            'ibu' => 'nullable|integer|min:0',
            'volume_ml' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock' => 'integer|min:0',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'image' => 'nullable|image|max:4096',
            'fixed_pack6_price' => 'nullable|numeric|min:0',
            'is_mix_premium' => 'boolean',
            'mix_surcharge_per_unit' => 'nullable|numeric|min:0',
            'variants' => 'array',
            'variants.*.label' => 'required_with:variants|string|max:255',
            'variants.*.price_override' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'integer|min:0',
        ]);

        unset($data['image']);
        $variants = $this->has_variants ? $data['variants'] : [];
        unset($data['variants']);

        $data['compare_at_price'] = $data['compare_at_price'] ?: null;
        $data['fixed_pack6_price'] = $data['fixed_pack6_price'] ?: null;
        $data['mix_surcharge_per_unit'] = $data['mix_surcharge_per_unit'] ?: null;

        if ($this->editingId) {
            $product = Product::findOrFail($this->editingId);
            $product->update($data);
        } else {
            $data['slug'] = Str::slug($this->name).'-'.Str::random(4);
            $product = Product::create($data);
        }

        if ($this->image) {
            $product->clearMediaCollection('images');
            $product->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('images');
        }

        $product->variants()->delete();

        foreach ($variants as $index => $variant) {
            $product->variants()->create([
                'label' => $variant['label'],
                'price_override' => $variant['price_override'] !== '' ? $variant['price_override'] : null,
                'stock' => $variant['stock'],
                'sort_order' => $index,
            ]);
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'category_id', 'name', 'style', 'description', 'abv', 'ibu',
            'volume_ml', 'price', 'compare_at_price', 'stock', 'is_popular', 'is_active',
            'sort_order', 'image', 'fixed_pack6_price', 'is_mix_premium', 'mix_surcharge_per_unit',
            'has_variants', 'variants',
        ]);
        $this->is_active = true;
        $this->stock = 0;
        $this->sort_order = 0;
    }

    public function render()
    {
        return view('livewire.admin.catalog.products', [
            'title' => 'Productos',
            'products' => Product::with('category')->orderBy('sort_order')->get(),
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }
}
