<?php

namespace Tests\Feature;

use App\Livewire\Admin\Reports;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase7SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        return $admin;
    }

    private function makeProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'Línea Cumbre',
            'slug' => 'linea-cumbre-'.uniqid(),
            'sort_order' => 1,
        ]);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Cumbre IPA',
            'slug' => 'cumbre-ipa-'.uniqid(),
            'style' => 'IPA',
            'description' => 'Test',
            'abv' => 6.0,
            'ibu' => 50,
            'volume_ml' => 355,
            'price' => 10.00,
            'stock' => 20,
            'is_active' => true,
            'is_popular' => false,
            'sort_order' => 1,
        ], $overrides));
    }

    private function makeOrder(array $overrides = []): Order
    {
        $createdAt = $overrides['created_at'] ?? null;
        unset($overrides['created_at']);

        $order = Order::create(array_merge([
            'status' => Order::STATUS_PAID,
            'customer_name' => 'Cliente Prueba',
            'customer_email' => 'cliente@example.com',
            'customer_phone' => '987654321',
            'customer_document_type' => Order::DOCUMENT_DNI,
            'customer_document_number' => '12345678',
            'shipping_address' => 'Av. Siempre Viva 123',
            'shipping_city' => 'Lima',
            'subtotal' => 100,
            'total' => 100,
        ], $overrides));

        if ($createdAt) {
            $order->forceFill(['created_at' => $createdAt])->save();
        }

        return $order;
    }

    public function test_guest_cannot_access_reports(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
    }

    public function test_admin_sees_revenue_and_order_totals_for_period(): void
    {
        $this->actingAsAdmin();

        $this->makeOrder(['total' => 150, 'created_at' => now()]);
        $this->makeOrder(['total' => 250, 'created_at' => now()]);

        Livewire::test(Reports::class)
            ->assertSee('S/ 400.00')
            ->assertSee('2');
    }

    public function test_cancelled_orders_are_excluded_from_revenue(): void
    {
        $this->actingAsAdmin();

        $this->makeOrder(['total' => 150, 'created_at' => now()]);
        $this->makeOrder(['total' => 999, 'status' => Order::STATUS_CANCELLED, 'created_at' => now()]);

        Livewire::test(Reports::class)
            ->assertViewHas('revenue', fn ($revenue) => (float) $revenue === 150.0)
            ->assertViewHas('ordersCount', 1);
    }

    public function test_orders_outside_the_selected_period_are_excluded(): void
    {
        $this->actingAsAdmin();

        $this->makeOrder(['total' => 150, 'created_at' => now()]);
        $this->makeOrder(['total' => 999, 'created_at' => now()->subDays(60)]);

        Livewire::test(Reports::class)
            ->set('from', now()->subDays(6)->toDateString())
            ->set('to', now()->toDateString())
            ->assertViewHas('revenue', fn ($revenue) => (float) $revenue === 150.0)
            ->assertViewHas('ordersCount', 1);
    }

    public function test_top_products_reflects_units_sold(): void
    {
        $this->actingAsAdmin();

        $productA = $this->makeProduct(['name' => 'Cumbre IPA']);
        $productB = $this->makeProduct(['name' => 'Huascarán Stout']);

        $order = $this->makeOrder(['created_at' => now()]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $productA->id,
            'name' => $productA->name,
            'unit_price' => 10,
            'quantity' => 5,
            'subtotal' => 50,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $productB->id,
            'name' => $productB->name,
            'unit_price' => 10,
            'quantity' => 2,
            'subtotal' => 20,
        ]);

        Livewire::test(Reports::class)
            ->assertSeeInOrder(['Cumbre IPA', 'Huascarán Stout']);
    }

    public function test_top_customers_are_ranked_by_total_spend(): void
    {
        $this->actingAsAdmin();

        $this->makeOrder(['customer_name' => 'Ana Torres', 'customer_email' => 'ana@example.com', 'total' => 500, 'created_at' => now()]);
        $this->makeOrder(['customer_name' => 'Luis Gómez', 'customer_email' => 'luis@example.com', 'total' => 100, 'created_at' => now()]);

        Livewire::test(Reports::class)
            ->assertSeeInOrder(['Ana Torres', 'Luis Gómez']);
    }

    public function test_low_stock_products_are_listed(): void
    {
        $this->actingAsAdmin();

        $this->makeProduct(['name' => 'Cumbre Escasa', 'stock' => 3]);
        $this->makeProduct(['name' => 'Cumbre Abundante', 'stock' => 50]);

        Livewire::test(Reports::class)
            ->assertSee('Cumbre Escasa')
            ->assertDontSee('Cumbre Abundante');
    }

    public function test_preset_updates_the_date_range(): void
    {
        $this->actingAsAdmin();

        $component = Livewire::test(Reports::class)
            ->call('setPreset', '7d');

        $this->assertSame(now()->subDays(6)->toDateString(), $component->get('from'));
        $this->assertSame(now()->toDateString(), $component->get('to'));
    }
}
