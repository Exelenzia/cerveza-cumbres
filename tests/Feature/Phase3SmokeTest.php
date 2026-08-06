<?php

namespace Tests\Feature;

use App\Livewire\Storefront\Cart;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Shop;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase3SmokeTest extends TestCase
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

    private function fakeCulqi(): void
    {
        Http::fake(['api.culqi.com/*' => Http::response(['id' => 'chr_test_123'], 200)]);
        config(['services.culqi.secret_key' => 'sk_test_fake']);
    }

    public function test_cart_applies_a_valid_order_coupon_and_shows_discount(): void
    {
        $product = $this->makeProduct(['price' => 100]);
        Coupon::create([
            'code' => 'CUMBRES10',
            'type' => Coupon::TYPE_PERCENTAGE,
            'value' => 10,
            'scope' => Coupon::SCOPE_ORDER,
            'is_active' => true,
        ]);

        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Cart::class)
            ->set('couponInput', 'cumbres10')
            ->call('applyCoupon')
            ->assertSet('couponError', null)
            ->assertSee('10.00');
    }

    public function test_cart_rejects_an_unknown_coupon_code(): void
    {
        $product = $this->makeProduct();
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Cart::class)
            ->set('couponInput', 'NOEXISTE')
            ->call('applyCoupon')
            ->assertSet('couponError', 'Este cupón no existe.');
    }

    public function test_cart_rejects_coupon_below_minimum_order_amount(): void
    {
        $product = $this->makeProduct(['price' => 10]);
        Coupon::create([
            'code' => 'MIN100',
            'type' => Coupon::TYPE_FIXED,
            'value' => 5,
            'scope' => Coupon::SCOPE_ORDER,
            'min_order_amount' => 100,
            'is_active' => true,
        ]);

        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Cart::class)
            ->set('couponInput', 'MIN100')
            ->call('applyCoupon')
            ->assertSet('couponError', 'El monto mínimo para este cupón es S/ 100.00.');
    }

    public function test_checkout_computes_shipping_cost_from_selected_zone_and_saves_it_on_the_order(): void
    {
        Setting::set('guest_checkout_enabled', '1');
        $this->fakeCulqi();

        $lima = ShippingZone::create([
            'name' => 'Lima',
            'cities' => 'Lima, Callao',
            'price' => 10,
            'eta_label' => '24 horas',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $interior = ShippingZone::create([
            'name' => 'Interior',
            'cities' => 'Cusco, Arequipa',
            'price' => 20,
            'eta_label' => '24-72 horas',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $product = $this->makeProduct(['price' => 30]);
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Checkout::class)
            ->assertSet('shipping_zone_id', $lima->id)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Cusco')
            ->set('shipping_zone_id', $interior->id)
            ->call('proceedToPayment')
            ->assertDispatched('open-culqi')
            ->call('pay', 'tok_test_abc');

        $order = Order::first();

        $this->assertSame($interior->id, $order->shipping_zone_id);
        $this->assertEquals(20.00, $order->shipping_cost);
        $this->assertEquals(50.00, $order->total);
    }

    public function test_checkout_applies_order_coupon_discount_and_registers_usage(): void
    {
        Setting::set('guest_checkout_enabled', '1');
        $this->fakeCulqi();

        $coupon = Coupon::create([
            'code' => 'CUMBRES10',
            'type' => Coupon::TYPE_PERCENTAGE,
            'value' => 10,
            'scope' => Coupon::SCOPE_ORDER,
            'is_active' => true,
        ]);

        $product = $this->makeProduct(['price' => 100]);
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Cart::class)->set('couponInput', 'CUMBRES10')->call('applyCoupon');

        Livewire::test(Checkout::class)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->call('pay', 'tok_test_abc');

        $order = Order::first();

        $this->assertSame('CUMBRES10', $order->coupon_code);
        $this->assertEquals(10.00, $order->discount_amount);
        $this->assertEquals(90.00, $order->total);
        $this->assertSame(1, $coupon->fresh()->used_count);
    }

    public function test_checkout_ignores_product_coupon_when_product_not_in_cart(): void
    {
        Setting::set('guest_checkout_enabled', '1');
        $this->fakeCulqi();

        $otherProduct = $this->makeProduct(['price' => 50]);
        Coupon::create([
            'code' => 'SOLOSTOUT',
            'type' => Coupon::TYPE_FIXED,
            'value' => 5,
            'scope' => Coupon::SCOPE_PRODUCT,
            'product_id' => $otherProduct->id,
            'is_active' => true,
        ]);

        $cartedProduct = $this->makeProduct(['price' => 20]);
        Livewire::test(Shop::class)->call('addToCart', 'product', $cartedProduct->id);

        // Coupon can't even be applied from the cart because the required product isn't present.
        Livewire::test(Cart::class)
            ->set('couponInput', 'SOLOSTOUT')
            ->call('applyCoupon')
            ->assertSet('couponError', 'Este cupón solo aplica a un producto que no está en tu carrito.');
    }

    public function test_admin_shipping_and_coupons_pages_render_for_admin(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $this->get(route('admin.shipping.zones'))->assertOk();
        $this->get(route('admin.coupons.index'))->assertOk();
    }
}
