<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
class Index extends Component
{
    #[Url]
    public string $status = '';

    public function render()
    {
        return view('livewire.admin.orders.index', [
            'title' => 'Pedidos',
            'orders' => Order::query()
                ->when($this->status, fn ($query) => $query->where('status', $this->status))
                ->latest()
                ->get(),
            'statuses' => Order::STATUSES,
        ]);
    }
}
