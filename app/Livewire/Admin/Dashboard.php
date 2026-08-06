<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Pack;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'title' => 'Dashboard',
            'categoriesCount' => Category::count(),
            'productsCount' => Product::count(),
            'packsCount' => Pack::count(),
            'lowStockProducts' => Product::where('is_active', true)->where('stock', '<=', 10)->orderBy('stock')->take(5)->get(),
        ]);
    }
}
