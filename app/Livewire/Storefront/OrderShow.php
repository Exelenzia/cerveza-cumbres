<?php

namespace App\Livewire\Storefront;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class OrderShow extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $isOwner = auth()->check() && $order->user_id === auth()->id();
        $isGuestOrder = $order->user_id === null;

        abort_unless($isOwner || $isGuestOrder, 403);

        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.storefront.order-show', [
            'title' => 'Pedido '.$this->order->order_number,
            'items' => $this->order->items()->with(['product', 'pack'])->get(),
        ]);
    }
}
