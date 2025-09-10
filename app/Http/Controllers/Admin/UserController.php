<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserNotification;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        try {
            // User statistics
            $users = User::paginate(10);
            $totalUsers = User::count();
            $suspendedUsers = User::where('is_suspended', true)->count();
            $usersByRole = User::groupBy('role')->select('role', \DB::raw('count(*) as count'))->get();
            $registrationTrends = User::select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            // Coupon statistics
            $totalCoupons = Coupon::count();
            $activeCoupons = Coupon::where('is_used', false)->where('expires_at', '>', now())->count();
            $usedCoupons = Coupon::where('is_used', true)->count();
            $expiredCoupons = Coupon::where('expires_at', '<=', now())->count();
            $couponTrends = Coupon::select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
            $couponsByPlatform = Meal::whereNotNull('share_link')
                ->whereNotNull('platform')
                ->get()
                ->groupBy('platform')
                ->map->count();

            Log::info('Admin dashboard loaded', [
                'user_id' => auth()->id(),
                'total_users' => $totalUsers,
                'total_coupons' => $totalCoupons,
                'active_coupons' => $activeCoupons,
            ]);

            return view('admin.dashboard', compact(
                'users',
                'totalUsers',
                'suspendedUsers',
                'usersByRole',
                'registrationTrends',
                'totalCoupons',
                'activeCoupons',
                'usedCoupons',
                'expiredCoupons',
                'couponTrends',
                'couponsByPlatform'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to load admin dashboard: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suspend a user.
     */
    public function suspend(Request $request, User $user)
    {
        try {
            $user->update(['is_suspended' => true]);
            Log::info('User suspended', [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'User suspended successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to suspend user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unsuspend a user.
     */
    public function unsuspend(Request $request, User $user)
    {
        try {
            $user->update(['is_suspended' => false]);
            Log::info('User unsuspended', [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'User unsuspended successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to unsuspend user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to unsuspend user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request, User $user)
    {
        try {
            $user->delete();
            Log::info('User deleted', [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send an email to a single user.
     */
    public function sendEmail(Request $request, User $user)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            Mail::to($user->email)->send(new UserNotification($request->subject, $request->message));
            Log::info('Email sent to user', [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
                'subject' => $request->subject,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send an email to all users.
     */
    public function sendEmailToAll(Request $request)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $users = User::all();
            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found to send emails to'
                ], 400);
            }

            foreach ($users as $user) {
                Mail::to($user->email)->queue(new UserNotification($request->subject, $request->message));
            }

            Log::info('Emails queued for all users', [
                'admin_id' => auth()->id(),
                'user_count' => $users->count(),
                'subject' => $request->subject,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Emails queued for sending to all users'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue emails: ' . $e->getMessage(), [
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue emails: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of all coupons.
     */
    public function couponsIndex()
    {
        $coupons = Coupon::with('user')->latest()->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function couponsCreate()
    {
        $users = User::select('id', 'name')->orderBy('name')->get();
        return view('admin.coupons.create', compact('users'));
    }

    /**
     * Store a newly created coupon.
     */
    public function couponsStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'required|date|after:now',
        ]);

        try {
            $coupon = Coupon::create([
                'user_id' => $request->input('user_id'),
                'code' => Str::random(10), // Generate a unique 10-character code
                'discount_percentage' => $request->input('discount_percentage'),
                'expires_at' => $request->input('expires_at'),
                'is_used' => false,
            ]);

            Log::info('Coupon created', [
                'coupon_id' => $coupon->id,
                'user_id' => $coupon->user_id,
                'admin_id' => auth()->id(),
                'discount_percentage' => $coupon->discount_percentage,
                'expires_at' => $coupon->expires_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Coupon created successfully',
                'redirect' => route('admin.coupons.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Coupon creation failed: ' . $e->getMessage(), [
                'user_id' => $request->input('user_id'),
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create coupon: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing a coupon.
     */
    public function couponsEdit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update a coupon's details.
     */
    public function couponsUpdate(Request $request, Coupon $coupon)
    {
        $request->validate([
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'required|date|after:now',
        ]);

        try {
            $coupon->update([
                'discount_percentage' => $request->input('discount_percentage'),
                'expires_at' => $request->input('expires_at'),
            ]);

            Log::info('Coupon updated', [
                'coupon_id' => $coupon->id,
                'admin_id' => auth()->id(),
                'changes' => $request->only('discount_percentage', 'expires_at'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Coupon updated successfully',
                'redirect' => route('admin.coupons.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Coupon update failed: ' . $e->getMessage(), [
                'coupon_id' => $coupon->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update coupon: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle a coupon's status (used/active).
     */
    public function couponsToggleStatus(Request $request, Coupon $coupon)
    {
        $request->validate([
            'is_used' => 'required|boolean',
        ]);

        try {
            $coupon->update(['is_used' => $request->input('is_used')]);

            Log::info('Coupon status toggled', [
                'coupon_id' => $coupon->id,
                'admin_id' => auth()->id(),
                'is_used' => $request->input('is_used'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Coupon status updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Coupon status toggle failed: ' . $e->getMessage(), [
                'coupon_id' => $coupon->id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle coupon status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display share submissions for review.
     */
    public function sharesIndex()
    {
        try {
            $shares = Meal::whereNotNull('share_link')
                ->whereNotNull('platform')
                ->with('user')
                ->latest()
                ->paginate(20);

            Log::info('Share submissions loaded for review', [
                'admin_id' => auth()->id(),
                'total_shares' => $shares->total(),
            ]);

            return view('admin.shares.index', compact('shares'));
        } catch (\Exception $e) {
            Log::error('Failed to load share submissions: ' . $e->getMessage(), [
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load share submissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a share submission and issue a coupon.
     */
    public function approveShare(Request $request, Meal $meal)
    {
        $request->validate([
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'required|date|after:now',
        ]);

        try {
            // Check if the share is already approved
            if (isset($meal->share_link['approved']) && $meal->share_link['approved']) {
                return response()->json([
                    'success' => false,
                    'message' => 'This share has already been approved'
                ], 400);
            }

            // Create a coupon
            $coupon = Coupon::create([
                'user_id' => $meal->user_id,
                'code' => Str::random(10),
                'discount_percentage' => $request->discount_percentage,
                'expires_at' => $request->expires_at,
                'is_used' => false,
            ]);

            // Mark the share as approved
            $meal->update([
                'share_link' => array_merge($meal->share_link, ['approved' => true])
            ]);

            // Notify the user via email
            Mail::to($meal->user->email)->queue(new UserNotification(
                'Your Share Has Been Approved!',
                'Your social media share for Meal #' . $meal->id . ' has been approved. A coupon (' . $coupon->code . ') with ' . $coupon->discount_percentage . '% off has been issued and can be applied to your subscription.'
            ));

            Log::info('Share approved and coupon issued', [
                'meal_id' => $meal->id,
                'user_id' => $meal->user_id,
                'coupon_id' => $coupon->id,
                'admin_id' => auth()->id(),
                'discount_percentage' => $coupon->discount_percentage,
                'expires_at' => $coupon->expires_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Share approved and coupon issued successfully',
                'redirect' => route('admin.shares.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to approve share and issue coupon: ' . $e->getMessage(), [
                'meal_id' => $meal->id,
                'user_id' => $meal->user_id,
                'admin_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve share: ' . $e->getMessage()
            ], 500);
        }
    }
}