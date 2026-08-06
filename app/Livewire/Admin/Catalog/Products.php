<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\Category;
use App\Models\Product;
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

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);

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
        $this->showForm = true;
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
        ]);

        unset($data['image']);
        $data['compare_at_price'] = $data['compare_at_price'] ?: null;

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
            'sort_order', 'image',
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
