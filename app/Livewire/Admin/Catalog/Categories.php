<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Categories extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public int $sort_order = 0;

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->description = (string) $category->description;
        $this->sort_order = $category->sort_order;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        $data['slug'] = Str::slug($this->name).($this->editingId ? '' : '-'.Str::random(4));

        if ($this->editingId) {
            $category = Category::findOrFail($this->editingId);
            unset($data['slug']);
            $category->update($data);
        } else {
            Category::create($data);
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'sort_order']);
    }

    public function render()
    {
        return view('livewire.admin.catalog.categories', [
            'title' => 'Categorías',
            'categories' => Category::withCount('products')->orderBy('sort_order')->get(),
        ]);
    }
}
