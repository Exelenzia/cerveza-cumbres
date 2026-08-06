<?php

namespace App\Livewire\Admin\Cms;

use App\Models\Page;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Pages extends Component
{
    private const RESERVED_SLUGS = [
        'tienda', 'carrito', 'checkout', 'pedidos', 'mis-pedidos', 'login', 'registro', 'admin', 'paginas',
    ];

    public bool $showForm = false;

    public string $title = '';

    public function create(): void
    {
        $this->reset(['title']);
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
        ]);

        $slug = Str::slug($data['title']);

        if (in_array($slug, self::RESERVED_SLUGS, true) || Page::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(4);
        }

        $page = Page::create([
            'title' => $data['title'],
            'slug' => $slug,
            'is_active' => true,
            'sort_order' => (int) Page::max('sort_order') + 1,
        ]);

        $this->reset(['title']);
        $this->showForm = false;

        $this->redirectRoute('admin.cms.pages.edit', $page);
    }

    public function toggleActive(int $id): void
    {
        $page = Page::findOrFail($id);
        $page->update(['is_active' => ! $page->is_active]);
    }

    public function delete(int $id): void
    {
        Page::findOrFail($id)->delete();
    }

    public function cancel(): void
    {
        $this->reset(['title']);
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.admin.cms.pages', [
            'title' => 'Páginas (CMS)',
            'pages' => Page::withCount('blocks')->orderBy('sort_order')->get(),
        ]);
    }
}
