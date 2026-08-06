<?php

namespace App\Livewire\Storefront;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Services\Cart\CartService;
use App\Services\Cart\CouponException;
use App\Services\Cart\CouponService;
use App\Services\Culqi\CulqiChargeException;
use App\Services\Culqi\CulqiService;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Checkout extends Component
{
    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_phone = '';

    public string $customer_document_type = Order::DOCUMENT_DNI;

    public string $customer_document_number = '';

    public string $shipping_address = '';

    public string $shipping_city = '';

    public string $notes = '';

    public ?int $shipping_zone_id = null;

    public ?string $paymentError = null;

    public bool $processing = false;

    public function mount(): void
    {
        if (app(CartService::class)->isEmpty()) {
            $this->redirectRoute('cart');

            return;
        }

        $guestCheckoutEnabled = Setting::bool('guest_checkout_enabled');

        if (! auth()->check() && ! $guestCheckoutEnabled) {
            session(['url.intended' => route('checkout')]);
            $this->redirectRoute('login');

            return;
        }

        if (auth()->check()) {
            $user = auth()->user();
            $this->customer_name = $user->name;
            $this->customer_email = $user->email;
            $this->customer_phone = (string) $user->phone;
            $this->shipping_address = (string) $user->address;
        }

        $firstZone = ShippingZone::where('is_active', true)->orderBy('sort_order')->first();
        $this->shipping_zone_id = $firstZone?->id;
    }

    public function proceedToPayment(): void
    {
        $this->paymentError = null;

        $hasZones = ShippingZone::where('is_active', true)->exists();

        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'customer_document_type' => 'required|in:dni,ruc',
            'customer_document_number' => $this->customer_document_type === Order::DOCUMENT_RUC
                ? 'required|digits:11'
                : 'required|digits:8',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
            'shipping_zone_id' => ($hasZones ? 'required|' : 'nullable|').'exists:shipping_zones,id',
        ]);

        if (app(CartService::class)->isEmpty()) {
            $this->redirectRoute('cart');

            return;
        }

        $this->dispatch('open-culqi');
    }

    public function setPaymentError(string $message): void
    {
        $this->paymentError = $message;
        $this->processing = false;
    }

    public function pay(string $token): void
    {
        $this->paymentError = null;
        $this->processing = true;

        $cart = app(CartService::class);
        $items = $cart->items();

        if ($items->isEmpty()) {
            $this->processing = false;
            $this->redirectRoute('cart');

            return;
        }

        $subtotal = $cart->subtotal();

        $coupon = null;
        $discount = 0.0;

        if ($code = $cart->couponCode()) {
            try {
                $coupon = app(CouponService::class)->validate($code, $subtotal, $items);
                $discount = $coupon->calculateDiscount($subtotal, $items);
            } catch (CouponException $e) {
                $cart->clearCoupon();
                $this->paymentError = $e->getMessage();
                $this->processing = false;

                return;
            }
        }

        $shippingZone = $this->shipping_zone_id ? ShippingZone::find($this->shipping_zone_id) : null;
        $shippingCost = $shippingZone ? (float) $shippingZone->price : 0.0;

        $total = round($subtotal - $discount + $shippingCost, 2);

        try {
            $chargeId = app(CulqiService::class)->charge($token, $total, $this->customer_email);
        } catch (CulqiChargeException $e) {
            $this->paymentError = $e->getMessage();
            $this->processing = false;

            return;
        }

        $order = DB::transaction(function () use ($items, $subtotal, $discount, $shippingCost, $shippingZone, $coupon, $total, $chargeId) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'shipping_zone_id' => $shippingZone?->id,
                'status' => Order::STATUS_PAID,
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone ?: null,
                'customer_document_type' => $this->customer_document_type,
                'customer_document_number' => $this->customer_document_number,
                'shipping_address' => $this->shipping_address,
                'shipping_city' => $this->shipping_city,
                'notes' => $this->notes ?: null,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'currency' => 'PEN',
                'payment_method' => 'culqi',
                'payment_reference' => $chargeId,
                'coupon_code' => $coupon?->code,
                'paid_at' => now(),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['type'] === 'product' ? $item['model']->id : null,
                    'pack_id' => $item['type'] === 'pack' ? $item['model']->id : null,
                    'name' => $item['model']->name,
                    'unit_price' => $item['unitPrice'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['lineTotal'],
                ]);
            }

            if ($coupon) {
                app(CouponService::class)->registerUsage($coupon);
            }

            return $order;
        });

        app(WhatsAppService::class)->notifyOrderConfirmed($order);

        $cart->clear();
        $this->dispatch('cart-updated');

        $this->redirectRoute('orders.show', $order);
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
            }
        }

        $shippingZones = ShippingZone::where('is_active', true)->orderBy('sort_order')->get();
        $selectedZone = $shippingZones->firstWhere('id', $this->shipping_zone_id);
        $shippingCost = $selectedZone ? (float) $selectedZone->price : 0.0;

        return view('livewire.storefront.checkout', [
            'title' => 'Checkout',
            'items' => $items,
            'subtotal' => $subtotal,
            'coupon' => $coupon,
            'discount' => $discount,
            'shippingZones' => $shippingZones,
            'shippingCost' => $shippingCost,
            'total' => round($subtotal - $discount + $shippingCost, 2),
            'culqiPublicKey' => config('services.culqi.public_key'),
        ]);
    }
}
