<?php

namespace App\Livewire\Storefront;

use App\Models\Coupon;
use App\Services\Cart\CartService;
use App\Services\Cart\CouponException;
use App\Services\Cart\CouponService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Cart extends Component
{
    public string $couponInput = '';

    public ?string $couponError = null;

    public function updateQuantity(string $key, int $quantity): void
    {
        app(CartService::class)->update($key, $quantity);
        $this->dispatch('cart-updated');
    }

    public function remove(string $key): void
    {
        app(CartService::class)->remove($key);
        $this->dispatch('cart-updated');
    }

    public function applyCoupon(CouponService $coupons): void
    {
        $this->couponError = null;
        $code = trim($this->couponInput);

        if ($code === '') {
            return;
        }

        $cart = app(CartService::class);

        try {
            $coupons->validate($code, $cart->subtotal(), $cart->items());
        } catch (CouponException $e) {
            $this->couponError = $e->getMessage();

            return;
        }

        $cart->setCouponCode($code);
        $this->couponInput = '';
    }

    public function removeCoupon(): void
    {
        app(CartService::class)->clearCoupon();
        $this->couponError = null;
    }

    public function render()
    {
        $cart = app(CartService::class);
        $items = $cart->items();
        $subtotal = $cart->subtotal();

        $coupon = null;
        $discount = 0.0;

        if ($code = $cart->couponCode()) {
            $coupon = Coupon::whereRaw('lower(code) = ?', [mb_strtolower($code)])->first();

            if ($coupon && $coupon->validationError($subtotal, $items) === null) {
                $discount = $coupon->calculateDiscount($subtotal, $items);
            } else {
                $coupon = null;
                $cart->clearCoupon();
            }
        }

        return view('livewire.storefront.cart', [
            'title' => 'Carrito',
            'items' => $items,
            'subtotal' => $subtotal,
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => round($subtotal - $discount, 2),
        ]);
    }
}
