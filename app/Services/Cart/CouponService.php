<?php

namespace App\Services\Cart;

use App\Models\Coupon;
use Illuminate\Support\Collection;

class CouponService
{
    /**
     * @param  Collection<int, array{type: string, model: mixed, lineTotal: float}>  $items
     *
     * @throws CouponException
     */
    public function validate(string $code, float $subtotal, Collection $items): Coupon
    {
        $coupon = Coupon::whereRaw('lower(code) = ?', [mb_strtolower(trim($code))])->first();

        if (! $coupon) {
            throw new CouponException('Este cupón no existe.');
        }

        $error = $coupon->validationError($subtotal, $items);

        if ($error !== null) {
            throw new CouponException($error);
        }

        return $coupon;
    }

    public function registerUsage(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}
