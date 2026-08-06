<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\Pack;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class Packs extends Component
{
    use WithFileUploads;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public string $price = '';

    public string $compare_at_price = '';

    public bool $is_active = true;

    public int $sort_order = 0;

    public $image = null;

    /** @var array<int, array{product_id: ?int, quantity: int}> */
    public array $items = [];

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $pack = Pack::with('products')->findOrFail($id);

        $this->editingId = $pack->id;
        $this->name = $pack->name;
        $this->description = (string) $pack->description;
        $this->price = (string) $pack->price;
        $this->compare_at_price = (string) $pack->compare_at_price;
        $this->is_active = $pack->is_active;
        $this->sort_order = $pack->sort_order;
        $this->image = null;
        $this->items = $pack->products->map(fn ($product) => [
            'product_id' => $product->id,
            'quantity' => $product->pivot->quantity,
        ])->toArray();

        if (empty($this->items)) {
            $this->addItem();
        }

        $this->showForm = true;
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => 1];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'image' => 'nullable|image|max:4096',
            'items' => 'array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        unset($data['image'], $data['items']);
        $data['compare_at_price'] = $data['compare_at_price'] ?: null;

        if ($this->editingId) {
            $pack = Pack::findOrFail($this->editingId);
            $pack->update($data);
        } else {
            $data['slug'] = Str::slug($this->name).'-'.Str::random(4);
            $pack = Pack::create($data);
        }

        $syncData = collect($this->items)
            ->filter(fn ($item) => filled($item['product_id']))
            ->mapWithKeys(fn ($item) => [$item['product_id'] => ['quantity' => $item['quantity']]])
            ->toArray();
        $pack->products()->sync($syncData);

        if ($this->image) {
            $pack->clearMediaCollection('images');
            $pack->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('images');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Pack::findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'price', 'compare_at_price', 'sort_order', 'image', 'items']);
        $this->is_active = true;
        $this->sort_order = 0;
        $this->addItem();
    }

    public function render()
    {
        return view('livewire.admin.catalog.packs', [
            'title' => 'Packs',
            'packs' => Pack::with('products')->orderBy('sort_order')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
