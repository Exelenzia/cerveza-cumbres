<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Show extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $this->order = $order;
    }

    public function updateStatus(string $status): void
    {
        if (! in_array($status, Order::STATUSES, true)) {
            return;
        }

        $this->order->update(['status' => $status]);
    }

    public function render()
    {
        return view('livewire.admin.orders.show', [
            'title' => 'Pedido '.$this->order->order_number,
            'items' => $this->order->items()->with(['product', 'pack'])->get(),
            'statuses' => Order::STATUSES,
        ]);
    }
}
