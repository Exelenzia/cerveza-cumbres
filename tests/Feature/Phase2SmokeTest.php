<?php

namespace Tests\Feature;

use App\Livewire\Storefront\Cart;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Shop;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase2SmokeTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_adds_product_to_cart_and_shows_it_in_cart_page(): void
    {
        $product = $this->makeProduct();

        Livewire::test(Shop::class)
            ->call('addToCart', 'product', $product->id)
            ->assertDispatched('cart-updated');

        Livewire::test(Cart::class)
            ->assertSee($product->name);
    }

    public function test_checkout_redirects_to_login_when_guest_checkout_disabled(): void
    {
        Setting::set('guest_checkout_enabled', '0');

        $product = $this->makeProduct();
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Checkout::class)
            ->assertRedirect(route('login'));

        $this->assertSame(route('checkout'), session('url.intended'));
    }

    public function test_guest_checkout_completes_paid_order_when_enabled(): void
    {
        Setting::set('guest_checkout_enabled', '1');

        Http::fake([
            'api.culqi.com/*' => Http::response(['id' => 'chr_test_123'], 200),
        ]);
        config(['services.culqi.secret_key' => 'sk_test_fake']);

        $product = $this->makeProduct(['price' => 25.00]);
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Checkout::class)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->assertDispatched('open-culqi')
            ->call('pay', 'tok_test_abc');

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertSame(Order::STATUS_PAID, $order->status);
        $this->assertEquals(25.00, $order->total);
        $this->assertSame('chr_test_123', $order->payment_reference);
        $this->assertSame(1, $order->items()->count());
    }

    public function test_order_show_authorizes_owner_and_blocks_other_users(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $order = Order::create([
            'user_id' => $owner->id,
            'status' => Order::STATUS_PAID,
            'customer_name' => 'Owner',
            'customer_email' => $owner->email,
            'shipping_address' => 'Calle 1',
            'shipping_city' => 'Lima',
            'subtotal' => 10,
            'shipping_cost' => 0,
            'total' => 10,
            'currency' => 'PEN',
        ]);

        $this->actingAs($other);
        $this->get(route('orders.show', $order))->assertForbidden();

        $this->actingAs($owner);
        $this->get(route('orders.show', $order))->assertOk();
    }

    public function test_admin_orders_and_settings_pages_render_for_admin(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $this->get(route('admin.orders.index'))->assertOk();
        $this->get(route('admin.settings.index'))->assertOk();
    }
}
