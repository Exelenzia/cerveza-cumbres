<?php

namespace App\Livewire\Storefront;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Orders extends Component
{
    public function render()
    {
        return view('livewire.storefront.orders', [
            'title' => 'Mis pedidos',
            'orders' => auth()->user()->orders()->latest()->get(),
        ]);
    }
}
