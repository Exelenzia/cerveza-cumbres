<?php

use App\Livewire\Admin\Catalog\Categories;
use App\Livewire\Admin\Catalog\Packs;
use App\Livewire\Admin\Catalog\Products;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Marketing\Coupons as AdminCoupons;
use App\Livewire\Admin\Orders\Index as AdminOrdersIndex;
use App\Livewire\Admin\Orders\Show as AdminOrdersShow;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Admin\Shipping\Zones as AdminShippingZones;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Storefront\Cart;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Home;
use App\Livewire\Storefront\OrderShow;
use App\Livewire\Storefront\Orders;
use App\Livewire\Storefront\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/tienda', Shop::class)->name('shop');
Route::get('/carrito', Cart::class)->name('cart');
Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/pedidos/{order}', OrderShow::class)->name('orders.show');

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
    Route::get('/pedidos', AdminOrdersIndex::class)->name('orders.index');
    Route::get('/pedidos/{order}', AdminOrdersShow::class)->name('orders.show');
    Route::get('/envios', AdminShippingZones::class)->name('shipping.zones');
    Route::get('/cupones', AdminCoupons::class)->name('coupons.index');
    Route::get('/configuracion', AdminSettings::class)->name('settings.index');
});
