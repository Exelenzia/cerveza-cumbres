<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Storefront\Cart;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Shop;
use App\Models\Category;
use App\Models\Order;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class Phase8AuditFixesTest extends TestCase
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

    public function test_login_is_throttled_after_repeated_failed_attempts(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        for ($i = 0; $i < 5; $i++) {
            Livewire::test(Login::class)
                ->set('email', $user->email)
                ->set('password', 'wrong-password')
                ->call('authenticate')
                ->assertHasErrors(['email']);
        }

        $component = Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'correct-password')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertStringContainsString('Demasiados intentos', $component->errors()->first('email'));
        $this->assertGuest();
    }

    public function test_out_of_stock_product_cannot_be_added_via_checkout(): void
    {
        Setting::set('guest_checkout_enabled', '1');

        $product = $this->makeProduct(['stock' => 1]);
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);
        Livewire::test(Cart::class)->call('updateQuantity', "product-{$product->id}", 5);

        Livewire::test(Checkout::class)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('customer_document_type', Order::DOCUMENT_DNI)
            ->set('customer_document_number', '12345678')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->assertNotDispatched('open-culqi')
            ->assertSet('paymentError', fn ($message) => str_contains($message, 'stock suficiente'));
    }

    public function test_successful_order_decrements_product_stock(): void
    {
        Setting::set('guest_checkout_enabled', '1');

        Http::fake([
            'api.culqi.com/*' => Http::response(['id' => 'chr_test_123'], 200),
        ]);
        config(['services.culqi.secret_key' => 'sk_test_fake']);

        $product = $this->makeProduct(['price' => 25.00, 'stock' => 5]);
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);
        Livewire::test(Cart::class)->call('updateQuantity', "product-{$product->id}", 2);

        Livewire::test(Checkout::class)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('customer_document_type', Order::DOCUMENT_DNI)
            ->set('customer_document_number', '12345678')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->call('pay', 'tok_test_abc');

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_failed_charge_releases_reserved_stock(): void
    {
        Setting::set('guest_checkout_enabled', '1');

        Http::fake([
            'api.culqi.com/*' => Http::response(['merchant_message' => 'Tarjeta rechazada'], 422),
        ]);
        config(['services.culqi.secret_key' => 'sk_test_fake']);

        $product = $this->makeProduct(['price' => 25.00, 'stock' => 5]);
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Checkout::class)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('customer_document_type', Order::DOCUMENT_DNI)
            ->set('customer_document_number', '12345678')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->call('pay', 'tok_test_abc');

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertNull(Order::first());
    }

    public function test_shop_page_disables_add_to_cart_for_out_of_stock_product(): void
    {
        $product = $this->makeProduct(['stock' => 0]);

        Livewire::test(Shop::class)
            ->assertSee('Agotado')
            ->assertDontSeeHtml('wire:click="addToCart(\'product\', '.$product->id.')"');
    }

    public function test_pack_is_out_of_stock_when_a_component_product_is_short(): void
    {
        $scarce = $this->makeProduct(['name' => 'Cumbre Escasa', 'stock' => 1]);
        $abundant = $this->makeProduct(['name' => 'Cumbre Abundante', 'stock' => 50]);

        $pack = Pack::create([
            'name' => 'Pack Explorador',
            'slug' => 'pack-explorador',
            'price' => 30,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $pack->products()->attach([
            $scarce->id => ['quantity' => 2],
            $abundant->id => ['quantity' => 1],
        ]);

        $this->assertTrue($pack->fresh()->load('products')->is_out_of_stock);
    }
}
