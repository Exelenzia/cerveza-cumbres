<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
class Reports extends Component
{
    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    public function mount(): void
    {
        $this->from = $this->from ?: now()->subDays(29)->toDateString();
        $this->to = $this->to ?: now()->toDateString();
    }

    public function setPreset(string $preset): void
    {
        $this->to = now()->toDateString();
        $this->from = match ($preset) {
            '7d' => now()->subDays(6)->toDateString(),
            '30d' => now()->subDays(29)->toDateString(),
            '90d' => now()->subDays(89)->toDateString(),
            'month' => now()->startOfMonth()->toDateString(),
            default => $this->from,
        };
    }

    private function period(): array
    {
        $from = Carbon::parse($this->from)->startOfDay();
        $to = Carbon::parse($this->to)->endOfDay();

        return [$from, $to];
    }

    private function paidOrders()
    {
        [$from, $to] = $this->period();

        return Order::query()
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->whereBetween('created_at', [$from, $to]);
    }

    public function render()
    {
        $orders = $this->paidOrders()->get();

        $revenue = $orders->sum('total');
        $ordersCount = $orders->count();
        $averageOrder = $ordersCount > 0 ? $revenue / $ordersCount : 0;

        $dailySales = $orders
            ->groupBy(fn (Order $order) => $order->created_at->toDateString())
            ->map(fn ($group) => ['date' => $group->first()->created_at->toDateString(), 'total' => $group->sum('total')])
            ->sortBy('date')
            ->values();
        $maxDaily = $dailySales->max('total') ?: 1;

        $orderIds = $orders->pluck('id');

        $topProducts = OrderItem::query()
            ->whereIn('order_id', $orderIds)
            ->whereNotNull('product_id')
            ->selectRaw('product_id, SUM(quantity) as units_sold, SUM(subtotal) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('units_sold')
            ->take(10)
            ->with('product')
            ->get();
        $maxUnits = $topProducts->max('units_sold') ?: 1;

        $topCustomers = $orders
            ->groupBy('customer_email')
            ->map(fn ($group) => [
                'name' => $group->first()->customer_name,
                'email' => $group->first()->customer_email,
                'orders_count' => $group->count(),
                'total' => $group->sum('total'),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values();

        $lowStockProducts = Product::where('is_active', true)->where('stock', '<=', 10)->orderBy('stock')->take(10)->get();

        return view('livewire.admin.reports', [
            'title' => 'Reportes',
            'revenue' => $revenue,
            'ordersCount' => $ordersCount,
            'averageOrder' => $averageOrder,
            'dailySales' => $dailySales,
            'maxDaily' => $maxDaily,
            'topProducts' => $topProducts,
            'maxUnits' => $maxUnits,
            'topCustomers' => $topCustomers,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
