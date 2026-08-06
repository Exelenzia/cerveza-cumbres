<?php

namespace Tests\Feature;

use App\Livewire\Admin\Orders\Show as AdminOrderShow;
use App\Livewire\Admin\Sunat\Settings as SunatSettingsPage;
use App\Models\DocumentSeries;
use App\Models\Order;
use App\Models\SunatSetting;
use App\Models\User;
use App\Services\Sunat\SunatInvoiceException;
use App\Services\Sunat\SunatInvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase4SmokeTest extends TestCase
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

    public function test_admin_can_view_and_save_sunat_settings(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.sunat.settings'))->assertOk();

        Livewire::test(SunatSettingsPage::class)
            ->set('ruc', '20000000001')
            ->set('razon_social', 'Cumbres SAC')
            ->set('sol_usuario', 'MODDATOS')
            ->set('sol_clave', 'moddatos')
            ->set('modo', SunatSetting::MODO_BETA)
            ->call('save');

        $setting = SunatSetting::current();

        $this->assertNotNull($setting);
        $this->assertSame('20000000001', $setting->ruc);
        $this->assertSame('Cumbres SAC', $setting->razon_social);
        $this->assertSame('moddatos', $setting->sol_clave);
    }

    public function test_saving_sunat_settings_again_without_password_keeps_existing_secret(): void
    {
        $this->actingAsAdmin();

        SunatSetting::create([
            'ruc' => '20000000001',
            'razon_social' => 'Cumbres SAC',
            'sol_usuario' => 'MODDATOS',
            'sol_clave' => 'moddatos',
            'modo' => SunatSetting::MODO_BETA,
            'is_active' => true,
        ]);

        Livewire::test(SunatSettingsPage::class)
            ->set('razon_social', 'Cumbres SAC Actualizado')
            ->call('save');

        $setting = SunatSetting::current();

        $this->assertSame('Cumbres SAC Actualizado', $setting->razon_social);
        $this->assertSame('moddatos', $setting->sol_clave);
    }

    public function test_document_series_correlativo_increments_atomically(): void
    {
        $series = DocumentSeries::create([
            'tipo_comprobante' => DocumentSeries::BOLETA,
            'serie' => 'B001',
            'correlativo' => 0,
            'is_active' => true,
        ]);

        $this->assertSame(1, $series->nextCorrelativo());
        $this->assertSame(2, $series->nextCorrelativo());
        $this->assertSame(2, $series->fresh()->correlativo);
    }

    public function test_issuing_an_invoice_without_sunat_settings_throws(): void
    {
        $order = Order::create([
            'customer_name' => 'Cliente Prueba',
            'customer_email' => 'cliente@example.com',
            'customer_document_type' => Order::DOCUMENT_DNI,
            'customer_document_number' => '12345678',
            'shipping_address' => 'Av. Siempre Viva 123',
            'shipping_city' => 'Lima',
            'subtotal' => 100,
            'total' => 100,
        ]);

        $this->expectException(SunatInvoiceException::class);

        app(SunatInvoiceService::class)->issue($order);
    }

    public function test_admin_order_detail_shows_invoice_error_when_sunat_not_configured(): void
    {
        $this->actingAsAdmin();

        $order = Order::create([
            'customer_name' => 'Cliente Prueba',
            'customer_email' => 'cliente@example.com',
            'customer_document_type' => Order::DOCUMENT_DNI,
            'customer_document_number' => '12345678',
            'shipping_address' => 'Av. Siempre Viva 123',
            'shipping_city' => 'Lima',
            'subtotal' => 100,
            'total' => 100,
        ]);

        Livewire::test(AdminOrderShow::class, ['order' => $order])
            ->assertSet('tipoComprobante', DocumentSeries::BOLETA)
            ->call('issueInvoice')
            ->assertSet('invoiceError', 'SUNAT no está configurado. Ingresa el RUC, credenciales SOL y certificado en el panel de administración.');

        $this->assertSame(0, $order->invoices()->count());
    }

    public function test_admin_uploading_pfx_certificate_is_converted_to_pem(): void
    {
        $this->actingAsAdmin();

        $pfxPath = $this->makeTestPfx('cumbres1234');

        Livewire::test(SunatSettingsPage::class)
            ->set('ruc', '20000000001')
            ->set('razon_social', 'Cumbres SAC')
            ->set('sol_usuario', 'MODDATOS')
            ->set('sol_clave', 'moddatos')
            ->set('certificado_password', 'cumbres1234')
            ->set('certificado', UploadedFile::fake()->createWithContent('certificado.pfx', file_get_contents($pfxPath)))
            ->call('save')
            ->assertSet('certificadoError', null);

        $setting = SunatSetting::current();

        $this->assertNotNull($setting->certificado_path);

        $pem = Storage::disk('local')->get($setting->certificado_path);

        $this->assertStringContainsString('-----BEGIN PRIVATE KEY-----', $pem);
        $this->assertStringContainsString('-----BEGIN CERTIFICATE-----', $pem);
    }

    public function test_admin_uploading_pfx_certificate_with_wrong_password_shows_error(): void
    {
        $this->actingAsAdmin();

        $pfxPath = $this->makeTestPfx('cumbres1234');

        Livewire::test(SunatSettingsPage::class)
            ->set('ruc', '20000000001')
            ->set('razon_social', 'Cumbres SAC')
            ->set('sol_usuario', 'MODDATOS')
            ->set('sol_clave', 'moddatos')
            ->set('certificado_password', 'clave-incorrecta')
            ->set('certificado', UploadedFile::fake()->createWithContent('certificado.pfx', file_get_contents($pfxPath)))
            ->call('save')
            ->assertSet('certificadoError', 'No se pudo leer el certificado .pfx. Verifica que la contraseña sea correcta.');

        $this->assertNull(SunatSetting::current()->certificado_path);
    }

    private function makeTestPfx(string $password): string
    {
        $config = [];

        if (DIRECTORY_SEPARATOR === '\\') {
            $cnf = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';

            if (file_exists($cnf)) {
                $config['config'] = $cnf;
            }
        }

        $key = openssl_pkey_new($config + ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);

        $csr = openssl_csr_new(['commonName' => 'MODDATOS'], $key, $config);
        $cert = openssl_csr_sign($csr, null, $key, 365, $config);

        openssl_x509_export($cert, $certPem);
        openssl_pkey_export($key, $keyPem, null, $config);
        openssl_pkcs12_export($certPem, $pfxContent, $keyPem, $password);

        $path = tempnam(sys_get_temp_dir(), 'test_cert').'.pfx';
        file_put_contents($path, $pfxContent);

        return $path;
    }
}
