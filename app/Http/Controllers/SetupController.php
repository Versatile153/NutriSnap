<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Exception;

class SetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get the Stripe Price ID for a given plan.
     */
    private function getPriceIdForPlan(string $plan): ?string
    {
        $planPrices = [
            'plus' => env('STRIPE_PRICE_PLUS', 'price_1RuraiFo5xO98pwdviz7VuYx'),
            'pro' => env('STRIPE_PRICE_PRO', 'price_1RusLqFo5xO98pwdBBNaGX9E'),
        ];

        return $planPrices[$plan] ?? null;
    }

    /**
     * Validate and apply a coupon, returning the discount percentage.
     */
    private function applyCoupon(string $code, int $userId): ?float
    {
        try {
            $coupon = Coupon::where('code', $code)
                ->where('user_id', $userId)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->first();

            if (!$coupon) {
                return null;
            }

            // Mark the coupon as used
            $coupon->update(['is_used' => true]);

            Log::info('Coupon applied in subscription', [
                'coupon_id' => $coupon->id,
                'user_id' => $userId,
                'discount_percentage' => $coupon->discount_percentage,
            ]);

            return $coupon->discount_percentage;
        } catch (Exception $e) {
            Log::error('Failed to apply coupon in subscription: ' . $e->getMessage(), [
                'user_id' => $userId,
                'code' => $code,
            ]);
            return null;
        }
    }

    public function settings()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function index()
    {
        $user = auth()->user();
        if ($user->profile) {
            return redirect()->route('dashboard');
        }
        return view('profile.edit', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'height' => 'required|numeric|min:100',
            'weight' => 'required|numeric|min:30',
            'goal' => 'required|in:weight_loss,maintain,weight_gain',
            'daily_calories' => 'required|integer|min:500',
            'health_conditions' => 'array',
            'health_conditions.*' => 'in:diabetes,hypertension',
            'plan' => 'required|in:free,plus,pro',
            'coupon_code' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        Profile::create([
            'user_id' => $user->id,
            'height' => $request->height,
            'weight' => $request->weight,
            'goal' => $request->goal,
            'daily_calories' => $request->daily_calories,
            'conditions' => $request->health_conditions ?? [], // Map to conditions
            'plan' => $request->plan,
        ]);

        if ($request->plan !== 'free') {
            try {
                $priceId = $this->getPriceIdForPlan($request->plan);
                if (!$priceId) {
                    throw new Exception("No valid price found for plan: {$request->plan}");
                }

                $checkoutBuilder = $user->newSubscription($request->plan, $priceId);

                // Apply coupon if provided
                if ($request->coupon_code) {
                    $discountPercentage = $this->applyCoupon($request->coupon_code, $user->id);
                    if ($discountPercentage) {
                        $stripeCoupon = Cashier::stripe()->coupons->create([
                            'percent_off' => $discountPercentage,
                            'duration' => 'once',
                            'name' => 'Cal AI Coupon ' . $request->coupon_code,
                        ]);
                        $checkoutBuilder->withCoupon($stripeCoupon->id);
                    } else {
                        return redirect()->route('setup.index')->with('error', 'Invalid or expired coupon code.');
                    }
                }

                return $checkoutBuilder->checkout([
                    'success_url' => route('dashboard') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('setup.index'),
                ]);
            } catch (Exception $e) {
                Log::error('Subscription creation failed in store', [
                    'user_id' => $user->id,
                    'plan' => $request->plan,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->route('setup.index')->with('error', 'Failed to initiate subscription. Please try again or contact support.');
            }
        }

        return redirect()->route('dashboard')->with('status', 'profile-created');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'email_notifications' => 'boolean',
            'plan' => 'required|in:free,plus,pro',
            'coupon_code' => 'nullable|string|max:255',
        ];

        if ($user->profile) {
            $rules = array_merge($rules, [
                'height' => 'nullable|numeric|min:100',
                'weight' => 'nullable|numeric|min:30',
                'goal' => 'nullable|in:weight_loss,maintain,weight_gain',
                'daily_calories' => 'nullable|integer|min:500',
                'health_conditions' => 'nullable|array',
                'health_conditions.*' => 'in:diabetes,hypertension',
            ]);
        }

        $validated = $request->validate($rules);

        // Update users table
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (Schema::hasColumn('users', 'email_notifications')) {
            $updateData['email_notifications'] = $request->boolean('email_notifications');
        }

        $user->update($updateData);

        // Handle email verification if email changed
        if ($user->email !== $validated['email'] && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        }

        // Update profile if it exists
        if ($user->profile) {
            $profileData = [
                'height' => $validated['height'] ?? $user->profile->height,
                'weight' => $validated['weight'] ?? $user->profile->weight,
                'goal' => $validated['goal'] ?? $user->profile->goal,
                'daily_calories' => $validated['daily_calories'] ?? $user->profile->daily_calories,
                'conditions' => $validated['health_conditions'] ?? $user->profile->conditions, // Map to conditions
                'plan' => $validated['plan'],
            ];

            $currentPlan = $user->profile->plan ?? 'free';
            $newPlan = $validated['plan'];

            // Handle subscription changes
            if ($newPlan !== $currentPlan && $newPlan !== 'free') {
                try {
                    if ($currentPlan !== 'free') {
                        $user->subscription($currentPlan)->cancelNow();
                    }
                    $priceId = $this->getPriceIdForPlan($newPlan);
                    if (!$priceId) {
                        throw new Exception("No valid price found for plan: $newPlan");
                    }

                    $checkoutBuilder = $user->newSubscription($newPlan, $priceId);

                    // Apply coupon if provided
                    if ($request->coupon_code) {
                        $discountPercentage = $this->applyCoupon($request->coupon_code, $user->id);
                        if ($discountPercentage) {
                            $stripeCoupon = Cashier::stripe()->coupons->create([
                                'percent_off' => $discountPercentage,
                                'duration' => 'once',
                                'name' => 'Cal AI Coupon ' . $request->coupon_code,
                            ]);
                            $checkoutBuilder->withCoupon($stripeCoupon->id);
                        } else {
                            return redirect()->route('settings')->with('error', 'Invalid or expired coupon code.');
                        }
                    }

                    // Update profile before checkout
                    $user->profile->update($profileData);

                    return $checkoutBuilder->checkout([
                        'success_url' => route('dashboard') . '?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => route('settings'),
                    ]);
                } catch (Exception $e) {
                    Log::error('Subscription update failed', [
                        'user_id' => $user->id,
                        'current_plan' => $currentPlan,
                        'new_plan' => $newPlan,
                        'error' => $e->getMessage(),
                    ]);
                    return redirect()->route('settings')->with('error', 'Failed to initiate subscription. Please try again or contact support.');
                }
            }

            // Update profile for free plan or no plan change
            $user->profile->update($profileData);
        }

        return redirect()->route('dashboard')->with('status', 'profile-updated');
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:plus,pro',
            'coupon_code' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $currentPlan = $user->profile->plan ?? 'free';

        if ($currentPlan === $request->plan) {
            return redirect()->route('settings')->with('error', 'You are already on the ' . ucfirst($request->plan) . ' plan.');
        }

        try {
            if ($currentPlan !== 'free') {
                $user->subscription($currentPlan)->cancelNow();
            }

            $priceId = $this->getPriceIdForPlan($request->plan);
            if (!$priceId) {
                throw new Exception("No valid price found for plan: {$request->plan}");
            }

            $checkoutBuilder = $user->newSubscription($request->plan, $priceId);

            // Apply coupon if provided
            if ($request->coupon_code) {
                $discountPercentage = $this->applyCoupon($request->coupon_code, $user->id);
                if ($discountPercentage) {
                    $stripeCoupon = Cashier::stripe()->coupons->create([
                        'percent_off' => $discountPercentage,
                        'duration' => 'once',
                        'name' => 'Cal AI Coupon ' . $request->coupon_code,
                    ]);
                    $checkoutBuilder->withCoupon($stripeCoupon->id);
                } else {
                    return redirect()->route('settings')->with('error', 'Invalid or expired coupon code.');
                }
            }

            $user->profile->update(['plan' => $request->plan]);

            return $checkoutBuilder->checkout([
                'success_url' => route('dashboard') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('settings'),
            ]);
        } catch (Exception $e) {
            Log::error('Subscription upgrade failed', [
                'user_id' => $user->id,
                'plan' => $request->plan,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('settings')->with('error', 'Failed to initiate subscription. Please try again or contact support.');
        }
    }
}


