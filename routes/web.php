<?php

use App\Livewire\Admin\Catalog\Categories;
use App\Livewire\Admin\Catalog\Packs;
use App\Livewire\Admin\Catalog\PackTemplates;
use App\Livewire\Admin\Catalog\Products;
use App\Livewire\Admin\Cms\PageEditor as AdminCmsPageEditor;
use App\Livewire\Admin\Cms\Pages as AdminCmsPages;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Marketing\Coupons as AdminCoupons;
use App\Livewire\Admin\Orders\Index as AdminOrdersIndex;
use App\Livewire\Admin\Orders\Show as AdminOrdersShow;
use App\Livewire\Admin\Reports as AdminReports;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Admin\Shipping\Zones as AdminShippingZones;
use App\Livewire\Admin\Sunat\Settings as AdminSunatSettings;
use App\Livewire\Admin\WhatsApp\Settings as AdminWhatsAppSettings;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Storefront\Cart;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Home;
use App\Livewire\Storefront\OrderShow;
use App\Livewire\Storefront\Orders;
use App\Livewire\Storefront\PackBuilder;
use App\Livewire\Storefront\PageShow;
use App\Livewire\Storefront\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/tienda', Shop::class)->name('shop');
Route::get('/tienda/armar-pack/{template:slug}', PackBuilder::class)->name('pack-builder.show');
Route::get('/carrito', Cart::class)->name('cart');
Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/pedidos/{order}', OrderShow::class)->name('orders.show');
Route::get('/paginas/{page:slug}', PageShow::class)->name('pages.show');

Route::middleware('auth')->group(function () {
    Route::get('/mis-pedidos', Orders::class)->name('orders.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/registro', Register::class)->name('register');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:admin|staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/categorias', Categories::class)->name('categories.index');
    Route::get('/productos', Products::class)->name('products.index');
    Route::get('/packs', Packs::class)->name('packs.index');
    Route::get('/armador-de-packs', PackTemplates::class)->name('pack-templates.index');
    Route::get('/pedidos', AdminOrdersIndex::class)->name('orders.index');
    Route::get('/pedidos/{order}', AdminOrdersShow::class)->name('orders.show');
    Route::get('/reportes', AdminReports::class)->name('reports.index');
    Route::get('/envios', AdminShippingZones::class)->name('shipping.zones');
    Route::get('/cupones', AdminCoupons::class)->name('coupons.index');
    Route::get('/paginas', AdminCmsPages::class)->name('cms.pages.index');
    Route::get('/paginas/{page}', AdminCmsPageEditor::class)->name('cms.pages.edit');
    Route::get('/facturacion-sunat', AdminSunatSettings::class)->name('sunat.settings');
    Route::get('/whatsapp', AdminWhatsAppSettings::class)->name('whatsapp.settings');
    Route::get('/configuracion', AdminSettings::class)->name('settings.index');
});
