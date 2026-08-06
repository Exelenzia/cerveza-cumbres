<?php

namespace Tests\Feature;

use App\Livewire\Admin\Orders\Show as AdminOrderShow;
use App\Livewire\Admin\WhatsApp\Settings as WhatsAppSettingsPage;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Shop;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhatsappSetting;
use App\Services\WhatsApp\WhatsAppException;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase5SmokeTest extends TestCase
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
        return Order::create(array_merge([
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
    }

    public function test_admin_can_view_and_save_whatsapp_settings(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.whatsapp.settings'))->assertOk();

        Livewire::test(WhatsAppSettingsPage::class)
            ->set('phone_number_id', '123456789')
            ->set('access_token', 'token-secreto')
            ->set('wa_link_phone', '51999999999')
            ->set('template_confirmacion', 'orden_confirmada')
            ->set('template_enviado', 'orden_enviada')
            ->set('template_entregado', 'orden_entregada')
            ->call('save');

        $setting = WhatsappSetting::current();

        $this->assertNotNull($setting);
        $this->assertSame('123456789', $setting->phone_number_id);
        $this->assertSame('token-secreto', $setting->access_token);
        $this->assertSame('51999999999', $setting->wa_link_phone);
    }

    public function test_saving_whatsapp_settings_again_without_token_keeps_existing_secret(): void
    {
        $this->actingAsAdmin();

        WhatsappSetting::create([
            'phone_number_id' => '123456789',
            'access_token' => 'token-original',
            'wa_link_phone' => '51999999999',
            'template_language' => 'es',
            'is_active' => true,
        ]);

        Livewire::test(WhatsAppSettingsPage::class)
            ->set('wa_link_phone', '51988888888')
            ->call('save');

        $setting = WhatsappSetting::current();

        $this->assertSame('51988888888', $setting->wa_link_phone);
        $this->assertSame('token-original', $setting->access_token);
    }

    public function test_whatsapp_service_sends_template_with_expected_payload(): void
    {
        WhatsappSetting::create([
            'phone_number_id' => '123456789',
            'access_token' => 'token-secreto',
            'template_language' => 'es',
            'is_active' => true,
        ]);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test']]], 200),
        ]);

        app(WhatsAppService::class)->sendTemplate('987654321', 'orden_confirmada', 'es', ['Cliente Prueba', 'ORD-1']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://graph.facebook.com/v20.0/123456789/messages'
                && $request->hasHeader('Authorization', 'Bearer token-secreto')
                && $request['to'] === '51987654321'
                && $request['type'] === 'template'
                && $request['template']['name'] === 'orden_confirmada'
                && $request['template']['components'][0]['parameters'][0]['text'] === 'Cliente Prueba';
        });
    }

    public function test_whatsapp_service_throws_when_not_configured(): void
    {
        $this->expectException(WhatsAppException::class);

        app(WhatsAppService::class)->sendTemplate('987654321', 'orden_confirmada', 'es');
    }

    public function test_whatsapp_service_throws_on_failed_response(): void
    {
        WhatsappSetting::create([
            'phone_number_id' => '123456789',
            'access_token' => 'token-secreto',
            'template_language' => 'es',
            'is_active' => true,
        ]);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'Plantilla no aprobada']], 400),
        ]);

        $this->expectException(WhatsAppException::class);
        $this->expectExceptionMessage('Plantilla no aprobada');

        app(WhatsAppService::class)->sendTemplate('987654321', 'orden_confirmada', 'es');
    }

    public function test_checkout_triggers_order_confirmed_whatsapp_notification(): void
    {
        Setting::set('guest_checkout_enabled', '1');

        WhatsappSetting::create([
            'phone_number_id' => '123456789',
            'access_token' => 'token-secreto',
            'template_language' => 'es',
            'template_confirmacion' => 'orden_confirmada',
            'is_active' => true,
        ]);

        Http::fake([
            'api.culqi.com/*' => Http::response(['id' => 'chr_test_123'], 200),
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test']]], 200),
        ]);
        config(['services.culqi.secret_key' => 'sk_test_fake']);

        $product = $this->makeProduct(['price' => 25.00]);
        Livewire::test(Shop::class)->call('addToCart', 'product', $product->id);

        Livewire::test(Checkout::class)
            ->set('customer_name', 'Cliente Prueba')
            ->set('customer_email', 'cliente@example.com')
            ->set('customer_phone', '987654321')
            ->set('customer_document_type', Order::DOCUMENT_DNI)
            ->set('customer_document_number', '12345678')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->call('pay', 'tok_test_abc');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'graph.facebook.com')
                && $request['template']['name'] === 'orden_confirmada';
        });
    }

    public function test_checkout_does_not_fail_when_whatsapp_is_not_configured(): void
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
            ->set('customer_document_type', Order::DOCUMENT_DNI)
            ->set('customer_document_number', '12345678')
            ->set('shipping_address', 'Av. Siempre Viva 123')
            ->set('shipping_city', 'Lima')
            ->call('proceedToPayment')
            ->call('pay', 'tok_test_abc');

        $this->assertNotNull(Order::first());
    }

    public function test_admin_updating_order_status_to_shipped_triggers_whatsapp_notification(): void
    {
        $this->actingAsAdmin();

        WhatsappSetting::create([
            'phone_number_id' => '123456789',
            'access_token' => 'token-secreto',
            'template_language' => 'es',
            'template_enviado' => 'orden_enviada',
            'is_active' => true,
        ]);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test']]], 200),
        ]);

        $order = $this->makeOrder();

        Livewire::test(AdminOrderShow::class, ['order' => $order])
            ->call('updateStatus', Order::STATUS_SHIPPED);

        $this->assertSame(Order::STATUS_SHIPPED, $order->fresh()->status);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'graph.facebook.com')
                && $request['template']['name'] === 'orden_enviada';
        });
    }

    public function test_admin_updating_order_status_to_delivered_triggers_whatsapp_notification(): void
    {
        $this->actingAsAdmin();

        WhatsappSetting::create([
            'phone_number_id' => '123456789',
            'access_token' => 'token-secreto',
            'template_language' => 'es',
            'template_entregado' => 'orden_entregada',
            'is_active' => true,
        ]);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test']]], 200),
        ]);

        $order = $this->makeOrder(['status' => Order::STATUS_SHIPPED]);

        Livewire::test(AdminOrderShow::class, ['order' => $order])
            ->call('updateStatus', Order::STATUS_DELIVERED);

        $this->assertSame(Order::STATUS_DELIVERED, $order->fresh()->status);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'graph.facebook.com')
                && $request['template']['name'] === 'orden_entregada';
        });
    }

    public function test_admin_updating_order_status_does_not_fail_when_whatsapp_is_not_configured(): void
    {
        $this->actingAsAdmin();

        $order = $this->makeOrder();

        Livewire::test(AdminOrderShow::class, ['order' => $order])
            ->call('updateStatus', Order::STATUS_SHIPPED);

        $this->assertSame(Order::STATUS_SHIPPED, $order->fresh()->status);
    }

    public function test_storefront_shows_whatsapp_chat_link_when_configured(): void
    {
        WhatsappSetting::create([
            'phone_number_id' => '123456789',
            'wa_link_phone' => '51999999999',
            'template_language' => 'es',
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('wa.me/51999999999', false);
    }

    public function test_storefront_hides_whatsapp_chat_link_when_not_configured(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('wa.me/', false);
    }
}
