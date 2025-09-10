<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Str;

class CouponService
{
    /**
     * Generate a new coupon for the user.
     */
    public function generateCoupon(User $user, int $discountPercentage, \DateTime $expiresAt): Coupon
    {
        return Coupon::create([
            'user_id' => $user->id,
            'code' => Str::random(10),
            'discount_percentage' => $discountPercentage,
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);
    }

    /**
     * Apply a coupon to the user's subscription.
     */
    public function applyCoupon(Coupon $coupon): void
    {
        $coupon->update(['is_used' => true]);

        // Integrate with Laravel Cashier
        $user = $coupon->user;
        if ($user->subscribed('default')) {
            $user->applyCoupon($coupon->code);
        }
    }
}