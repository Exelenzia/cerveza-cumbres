<?php

namespace Tests\Feature;

use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\PackBuilder;
use App\Livewire\Storefront\Shop;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PackTemplate;
use App\Models\Product;
use App\Models\Setting;
use App\Services\Cart\CartService;
use App\Services\Packs\PackPricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class PackBuilderTest extends TestCase
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

    private function makeTemplate(string $type, array $overrides = [], array $eligibleProductIds = []): PackTemplate
    {
        $template = PackTemplate::create(array_merge([
            'name' => 'Pack de prueba',
            'slug' => 'pack-de-prueba-'.uniqid(),
            'type' => $type,
            'bottle_count' => 6,
            'base_price' => 100,
            'is_active' => true,
            'sort_order' => 1,
        ], $overrides));

        if (! empty($eligibleProductIds)) {
            $template->eligibleProducts()->attach($eligibleProductIds);
        }

        return $template;
    }

    public function test_fixed_style_price_comes_from_the_chosen_products_fixed_price(): void
    {
        $product = $this->makeProduct(['fixed_pack6_price' => 55.00]);
        $template = $this->makeTemplate(PackTemplate::TYPE_FIXED_STYLE, ['bottle_count' => 6], [$product->id]);

        $price = app(PackPricingService::class)->price($template, [$product->id => 6]);

        $this->assertSame(55.0, $price);
    }

    public function test_mix_price_adds_surcharge_for_premium_styles(): void
    {
        $regular = $this->makeProduct(['name' => 'Cumbre Blonde']);
        $premium = $this->makeProduct([
            'name' => 'Cumbre Imperial Stout',
            'is_mix_premium' => true,
            'mix_surcharge_per_unit' => 3.00,
        ]);

        $template = $this->makeTemplate(
            PackTemplate::TYPE_MIX,
            ['bottle_count' => 12, 'base_price' => 115.00],
            [$regular->id, $premium->id]
        );

        $price = app(PackPricingService::class)->price($template, [
            $regular->id => 8,
            $premium->id => 4,
        ]);

        $this->assertSame(127.0, $price);
    }

    public function test_gift_price_is_flat_regardless_of_style_choice(): void
    {
        $a = $this->makeProduct(['name' => 'Cumbre A']);
        $b = $this->makeProduct(['name' => 'Cumbre B']);

        $template = $this->makeTemplate(
            PackTemplate::TYPE_GIFT,
            ['bottle_count' => 2, 'base_price' => 45.00],
            [$a->id, $b->id]
        );

        $price = app(PackPricingService::class)->price($template, [$a->id => 1, $b->id => 1]);

        $this->assertSame(45.0, $price);
    }

    public function test_validation_rejects_incomplete_selection(): void
    {
        $product = $this->makeProduct();
        $template = $this->makeTemplate(PackTemplate::TYPE_MIX, ['bottle_count' => 12], [$product->id]);

        $error = app(PackPricingService::class)->validateSelections($template, [$product->id => 6]);

        $this->assertNotNull($error);
        $this->assertStringContainsString('12 unidades', $error);
    }

    public function test_validation_rejects_a_style_not_eligible_for_the_template(): void
    {
        $eligible = $this->makeProduct(['name' => 'Cumbre Elegible']);
        $outsider = $this->makeProduct(['name' => 'Cumbre Fuera']);

        $template = $this->makeTemplate(PackTemplate::TYPE_MIX, ['bottle_count' => 6], [$eligible->id]);

        $error = app(PackPricingService::class)->validateSelections($template, [$outsider->id => 6]);

        $this->assertNotNull($error);
        $this->assertStringContainsString('no está disponible', $error);
    }

    public function test_checkout_creates_order_item_with_composition_and_decrements_selected_products(): void
    {
        Setting::set('guest_checkout_enabled', '1');

        Http::fake([
            'api.culqi.com/*' => Http::response(['id' => 'chr_test_123'], 200),
        ]);
        config(['services.culqi.secret_key' => 'sk_test_fake']);

        $ipa = $this->makeProduct(['name' => 'Cumbre IPA', 'stock' => 10]);
        $stout = $this->makeProduct(['name' => 'Cumbre Stout', 'stock' => 10]);

        $template = $this->makeTemplate(
            PackTemplate::TYPE_MIX,
            ['bottle_count' => 6, 'base_price' => 60.00],
            [$ipa->id, $stout->id]
        );

        app(CartService::class)->addCustomPack($template->id, [$ipa->id => 4, $stout->id => 2]);

        Livewire::test(Checkout::class)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('customer_document_type', Order::DOCUMENT_DNI)
            ->set('customer_document_number', '12345678')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->call('pay', 'tok_test_abc');

        $this->assertSame(6, $ipa->fresh()->stock);
        $this->assertSame(8, $stout->fresh()->stock);

        $orderItem = OrderItem::first();
        $this->assertNotNull($orderItem);
        $this->assertSame($template->id, $orderItem->pack_template_id);

        $composition = collect($orderItem->composition)->keyBy('product_id');
        $this->assertSame(4, $composition[$ipa->id]['quantity']);
        $this->assertSame(2, $composition[$stout->id]['quantity']);
    }

    public function test_out_of_stock_variant_blocks_checkout(): void
    {
        Setting::set('guest_checkout_enabled', '1');

        $product = $this->makeProduct();
        $variant = $product->variants()->create([
            'label' => 'L',
            'stock' => 0,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id, $variant->id);

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

    public function test_failed_charge_releases_reserved_product_and_variant_stock(): void
    {
        Setting::set('guest_checkout_enabled', '1');

        Http::fake([
            'api.culqi.com/*' => Http::response(['merchant_message' => 'Tarjeta rechazada'], 422),
        ]);
        config(['services.culqi.secret_key' => 'sk_test_fake']);

        $product = $this->makeProduct(['stock' => 5]);
        $variant = $product->variants()->create([
            'label' => 'L',
            'stock' => 5,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id, $variant->id);

        Livewire::test(Checkout::class)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('customer_document_type', Order::DOCUMENT_DNI)
            ->set('customer_document_number', '12345678')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->call('pay', 'tok_test_abc');

        $this->assertSame(5, $variant->fresh()->stock);
        $this->assertNull(Order::first());
    }

    public function test_pack_builder_adds_custom_pack_to_cart(): void
    {
        $ipa = $this->makeProduct(['name' => 'Cumbre IPA', 'stock' => 10]);
        $stout = $this->makeProduct(['name' => 'Cumbre Stout', 'stock' => 10]);

        $template = $this->makeTemplate(
            PackTemplate::TYPE_MIX,
            ['bottle_count' => 4, 'base_price' => 40.00],
            [$ipa->id, $stout->id]
        );

        Livewire::test(PackBuilder::class, ['template' => $template])
            ->call('increment', $ipa->id)
            ->call('increment', $ipa->id)
            ->call('increment', $stout->id)
            ->call('increment', $stout->id)
            ->call('addToCart')
            ->assertSet('error', null);

        $items = app(CartService::class)->items();
        $this->assertCount(1, $items);
        $this->assertSame('custom_pack', $items->first()['type']);
        $this->assertSame(40.0, $items->first()['unitPrice']);
    }
}
