<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's active coupons and share submissions.
     */
    public function index()
    {
        try {
            $activeCoupons = Coupon::where('user_id', auth()->id())
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->get();

            // Fetch share submissions (meals with share_link or share_proof)
            $shareSubmissions = Meal::where('user_id', auth()->id())
                ->where(function ($query) {
                    $query->whereNotNull('share_link')
                          ->orWhereNotNull('share_proof');
                })
                ->whereNotNull('platform')
                ->latest()
                ->get();

            Log::info('Coupons index loaded', [
                'user_id' => auth()->id(),
                'coupon_count' => $activeCoupons->count(),
                'share_submission_count' => $shareSubmissions->count(),
            ]);

            return view('coupons.index', compact('activeCoupons', 'shareSubmissions'));
        } catch (\Exception $e) {
            Log::error('Failed to load coupons index: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return redirect()->route('dashboard')->with('error', 'Failed to load coupons page: ' . $e->getMessage());
        }
    }

    /**
     * Apply a coupon to a subscription.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        try {
            $coupon = Coupon::where('code', $request->input('code'))
                ->where('user_id', auth()->id())
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->first();

            if (!$coupon) {
                return redirect()->route('coupons.index')->with('error', 'Invalid or expired coupon code.');
            }

            // Mark the coupon as used
            $coupon->update(['is_used' => true]);

            Log::info('Coupon applied', [
                'coupon_id' => $coupon->id,
                'user_id' => auth()->id(),
                'discount_percentage' => $coupon->discount_percentage,
            ]);

            // Store the discount percentage in the session for the subscription process
            session()->put('coupon_discount', $coupon->discount_percentage);

            return redirect()->route('settings')->with('status', 'coupon-applied');
        } catch (\Exception $e) {
            Log::error('Failed to apply coupon: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'code' => $request->input('code'),
            ]);
            return redirect()->route('coupons.index')->with('error', 'Failed to apply coupon: ' . $e->getMessage());
        }
    }

    /**
     * Submit a meal share for admin review.
     */
    public function share(Request $request)
    {
        $request->validate([
            'meal_id' => 'required|exists:meals,id',
            'platform' => 'required|in:twitter,facebook,instagram',
            'share_link' => 'required|url|max:255',
        ]);

        try {
            $meal = Meal::where('id', $request->meal_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $meal->update([
                'share_link' => ['url' => $request->share_link],
                'platform' => $request->platform,
            ]);

            Log::info('Meal share submitted for review', [
                'user_id' => auth()->id(),
                'meal_id' => $meal->id,
                'platform' => $request->platform,
                'share_link' => $request->share_link,
            ]);

            return redirect()->route('coupons.index')->with('status', 'share-submitted');
        } catch (\Exception $e) {
            Log::error('Failed to submit meal share: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'meal_id' => $request->meal_id,
            ]);
            return redirect()->route('coupons.index')->with('error', 'Failed to submit share: ' . $e->getMessage());
        }
    }
}