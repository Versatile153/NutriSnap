<?php

namespace App\Http\Controllers;

use App\Models\CorrectionRequest;
use App\Models\Meal;
use App\Models\Profile;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use App\Models\User;
use App\Models\Subscription;


use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
   

    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->query('period', '1month');
        $days = match ($period) {
            '1week' => 7,
            '3months' => 90,
            default => 30,
        };

        // Fetch profile
        $profile = Profile::where('user_id', $user->id)->first();

        // Fetch meals
        $meals = Meal::where('user_id', $user->id)
            ->whereDate('created_at', '>=', now()->subDays($days))
            ->latest()
            ->get();

        // Fetch correction requests
        $correctionRequests = CorrectionRequest::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        // Fetch active coupons
        $activeCoupons = Coupon::where('user_id', $user->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->get();

        // Calculate metrics
        $totalIntake = $meals->sum('calories');
        $dailyAvg = $days > 0 ? $totalIntake / $days : 0;
        $goalProgress = $profile && $profile->daily_calories > 0 ? ($dailyAvg / $profile->daily_calories) * 100 : 0;
        $caloriesBurned = $profile ? $profile->daily_calories * 0.2 * $days : 0;
        $totalMeals = $meals->count();
        $mealsToday = $meals->where('created_at', '>=', now()->startOfDay())->count();
        $pendingMeals = $meals->where('status', 'pending')->count();
        $goalAchieved = $profile && $profile->daily_calories > 0 ? min(($totalIntake / ($profile->daily_calories * $days)) * 100, 100) : 0;
        $daysActive = $meals->groupBy(function ($meal) {
            return $meal->created_at->format('Y-m-d');
        })->count();
        $weightChange = $profile ? $profile->weight - ($profile->initial_weight ?? $profile->weight) : 0;

        // Macronutrient trends by meal type (from analysis field)
        $macroTrends = $meals->groupBy('meal_type')->map(function ($mealsByType) {
            return [
                'meal_type' => $mealsByType->first()->meal_type,
                'protein' => $mealsByType->sum(function ($meal) {
                    return isset($meal->analysis['protein']) ? (float) $meal->analysis['protein'] : 0;
                }),
                'carbs' => $mealsByType->sum(function ($meal) {
                    return isset($meal->analysis['carbs']) ? (float) $meal->analysis['carbs'] : 0;
                }),
                'fat' => $mealsByType->sum(function ($meal) {
                    return isset($meal->analysis['fat']) ? (float) $meal->analysis['fat'] : 0;
                }),
            ];
        })->values();

        // Meal type distribution
        $mealTypeDistribution = [
            'breakfast' => $meals->where('meal_type', 'breakfast')->count(),
            'lunch' => $meals->where('meal_type', 'lunch')->count(),
            'dinner' => $meals->where('meal_type', 'dinner')->count(),
        ];

        // Calorie trend
        $calorieTrend = Meal::where('user_id', $user->id)
            ->whereDate('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                \DB::raw('DATE(created_at) as date'),
                \DB::raw('SUM(calories) as total'),
            ]);

        // Meal photos by type
        $breakfastMeals = $meals->where('meal_type', 'breakfast')->pluck('photo_url')->take(3);
        $lunchMeals = $meals->where('meal_type', 'lunch')->pluck('photo_url')->take(3);
        $dinnerMeals = $meals->where('meal_type', 'dinner')->pluck('photo_url')->take(3);

        Log::info('Admin dashboard loaded', [
            'user_id' => $user->id,
            'period' => $period,
            'meal_count' => $totalMeals,
            'correction_requests_count' => $correctionRequests->count(),
            'coupon_count' => $activeCoupons->count(),
        ]);

        return view('dashboard', compact(
            'user',
            'profile',
            'period',
            'totalIntake',
            'dailyAvg',
            'goalProgress',
            'caloriesBurned',
            'totalMeals',
            'mealsToday',
            'pendingMeals',
            'goalAchieved',
            'daysActive',
            'weightChange',
            'macroTrends',
            'mealTypeDistribution',
            'calorieTrend',
            'breakfastMeals',
            'lunchMeals',
            'dinnerMeals',
            'activeCoupons',
            'correctionRequests'
        ));
    }
    
   
    public function progress(Request $request, $user_id)
    {
        Log::info('Admin Progress Accessed', [
            'admin_id' => Auth::guard('admin')->id(),
            'user_id' => $user_id,
            'session' => $request->session()->all(),
        ]);

        // Fetch user and profile with error handling
        $user = User::find($user_id);
        if (!$user) {
            Log::warning('User not found', ['user_id' => $user_id]);
            return view('admin.progress', [
                'title' => __('Progress Overview'),
                'error' => __('User not found.'),
            ]);
        }

        $profile = Profile::where('user_id', $user_id)->first();
        if (!$profile) {
            Log::warning('Profile not found for user', ['user_id' => $user_id]);
            return view('admin.progress', [
                'title' => __('Progress Overview for :name', ['name' => $user->name]),
                'user' => $user,
                'error' => __('Profile not found.'),
            ]);
        }

        // Get meals for today
        $today = Carbon::today();
        $meals = Meal::where('user_id', $user_id)
            ->whereDate('created_at', $today)
            ->get();

        // Calorie compliance
        $totalCalories = $meals->sum('calories');
        $dailyCalorieGoal = $profile->daily_calories ?? 0;
        $calorieCompliance = $dailyCalorieGoal > 0 ? ($totalCalories <= $dailyCalorieGoal ? 'Compliant' : 'Over Limit') : 'No Goal Set';

        // Dietary compliance
        $conditions = $profile->conditions ?? [];
        $dietaryCompliance = 'Compliant';
        $nonCompliantMeals = [];

        foreach ($meals as $meal) {
            $mealAnalysis = $meal->analysis ?? [];
            foreach ($conditions as $condition) {
                if ($this->violatesCondition($mealAnalysis, $condition)) {
                    $dietaryCompliance = 'Non-Compliant';
                    $nonCompliantMeals[] = [
                        'meal_id' => $meal->id,
                        'food' => $mealAnalysis['food'] ?? 'Unknown',
                        'condition' => $condition,
                    ];
                }
            }
        }

        // Additional metrics
        $goalAchieved = 'Not Available';
        $weightChange = 0;
        if (is_numeric($profile->weight) && is_numeric($profile->target_weight)) {
            $goalAchieved = $profile->weight <= $profile->target_weight ? 'Achieved' : 'Not Achieved';
            $weightChange = $profile->weight - $profile->target_weight;
        } elseif ($profile->weight || $profile->target_weight) {
            Log::warning('Invalid weight data', [
                'user_id' => $user_id,
                'weight' => $profile->weight,
                'target_weight' => $profile->target_weight,
            ]);
        }

        $daysActive = Meal::where('user_id', $user_id)
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->count();

        // Macro trends with robust type checking
        $macroTrends = ['protein' => 0, 'carbs' => 0, 'fat' => 0];
        foreach ($meals as $meal) {
            $analysis = $meal->analysis ?? [];
            if (isset($analysis['macronutrients']) && is_array($analysis['macronutrients'])) {
                foreach (['protein', 'carbs', 'fat'] as $macro) {
                    $value = $analysis['macronutrients'][$macro] ?? 0;
                    if (is_numeric($value)) {
                        $macroTrends[$macro] += $value;
                    } else {
                        Log::warning('Invalid macronutrient data', [
                            'user_id' => $user_id,
                            'meal_id' => $meal->id,
                            'macronutrient' => $macro,
                            'value' => $value,
                        ]);
                    }
                }
            }
        }

        $mealTypeDistribution = [
            'breakfast' => Meal::where('user_id', $user_id)->where('meal_type', 'breakfast')->count(),
            'lunch' => Meal::where('user_id', $user_id)->where('meal_type', 'lunch')->count(),
            'dinner' => Meal::where('user_id', $user_id)->where('meal_type', 'dinner')->count(),
        ];

        $calorieTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyCalories = Meal::where('user_id', $user_id)
                ->whereDate('created_at', $date)
                ->sum('calories');
            $calorieTrend[$date->format('Y-m-d')] = $dailyCalories;
        }

        // Coupon count with error handling
        $activeCoupons = 0;
        try {
            $activeCoupons = Coupon::where('user_id', $user_id)
                ->where('expires_at', '>=', Carbon::now())
                ->count(); // Removed status filter
        } catch (QueryException $e) {
            Log::error('Failed to fetch active coupons', [
                'user_id' => $user_id,
                'error' => $e->getMessage(),
            ]);
            $activeCoupons = 'Not available';
        }

        // Correction request count with error handling
        $correctionRequests = 0;
        try {
            $correctionRequests = CorrectionRequest::whereHas('meal', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->where('status', 'pending')->count();
        } catch (QueryException $e) {
            Log::error('Failed to fetch correction requests', [
                'user_id' => $user_id,
                'error' => $e->getMessage(),
            ]);
            $correctionRequests = 'Not available';
        }

        return view('admin.progress', [
            'title' => __('Progress Overview for :name', ['name' => $user->name]),
            'user' => $user,
            'profile' => $profile,
            'meals' => $meals,
            'totalCalories' => $totalCalories,
            'dailyCalorieGoal' => $dailyCalorieGoal,
            'calorieCompliance' => $calorieCompliance,
            'dietaryCompliance' => $dietaryCompliance,
            'nonCompliantMeals' => $nonCompliantMeals,
            'goalAchieved' => $goalAchieved,
            'daysActive' => $daysActive,
            'weightChange' => $weightChange,
            'macroTrends' => $macroTrends,
            'mealTypeDistribution' => $mealTypeDistribution,
            'calorieTrend' => $calorieTrend,
            'breakfastMeals' => $mealTypeDistribution['breakfast'],
            'lunchMeals' => $mealTypeDistribution['lunch'],
            'dinnerMeals' => $mealTypeDistribution['dinner'],
            'activeCoupons' => $activeCoupons,
            'correctionRequests' => $correctionRequests,
        ]);
    }

    private function violatesCondition($mealAnalysis, $condition)
    {
        $food = strtolower($mealAnalysis['food'] ?? '');
        switch (strtolower($condition)) {
            case 'lactose intolerant':
                return strpos($food, 'milk') !== false || strpos($food, 'cheese') !== false || strpos($food, 'dairy') !== false;
            case 'gluten free':
                return strpos($food, 'bread') !== false || strpos($food, 'pasta') !== false || strpos($food, 'wheat') !== false;
            case 'vegetarian':
                return strpos($food, 'chicken') !== false || strpos($food, 'beef') !== false || strpos($food, 'fish') !== false;
            default:
                return false;
        }
    }
    
    
 public function analysis()
    {
        // User analytics
        $totalUsers = User::count();
        $activeUsers = User::where('is_suspended', false)->count();
        $suspendedUsers = User::where('is_suspended', true)->count();
        $users = User::select('id', 'name', 'email', 'is_suspended', 'last_login_at', 'created_at')->get();
        $signupTrend = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        $loginTrend = User::selectRaw('DATE(last_login_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->where('last_login_at', '>=', Carbon::now()->subDays(7))
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Subscription analytics
        $activeSubscriptions = Subscription::where('stripe_status', 'active')->count();
        $trialSubscriptions = Subscription::where('stripe_status', 'trialing')->count();
        $expiredSubscriptions = Subscription::where('stripe_status', 'canceled')->count();
        $subscriptions = Subscription::select('id', 'user_id', 'name', 'stripe_status', 'created_at')->get();

        // Meal analytics
        $mealTypeDistribution = [
            'Breakfast' => Meal::where('meal_type', 'breakfast')->count(),
            'Lunch' => Meal::where('meal_type', 'lunch')->count(),
            'Dinner' => Meal::where('meal_type', 'dinner')->count(),
        ];
        $calorieTrend = Meal::selectRaw('DATE(created_at) as date, SUM(calories) as total')
            ->groupBy('date')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get()
            ->pluck('total', 'date')
            ->toArray();
        $meals = Meal::select('id', 'user_id', 'meal_type', 'calories', 'status', 'created_at')->get();
        $dietaryDistribution = [
            'Vegan' => Meal::where('health_condition', 'like', '%vegan%')->count(),
            'Gluten Free' => Meal::where('health_condition', 'like', '%gluten free%')->count(),
            'Vegetarian' => Meal::where('health_condition', 'like', '%vegetarian%')->count(),
        ];
        $mealStatusDistribution = [
            'Pending' => Meal::where('status', 'pending')->count(),
            'Approved' => Meal::where('status', 'approved')->count(),
        ];

        // Coupon analytics
        $totalCoupons = Coupon::count();
        $activeCoupons = Coupon::where('status', 'active')->count();
        $usedCoupons = Coupon::where('status', 'used')->count();
        $couponRedemptionRate = $totalCoupons ? ($usedCoupons / $totalCoupons) * 100 : 0;
        $coupons = Coupon::select('id', 'code', 'status', 'created_at')->get();

        // Profile analytics
        $profiles = Profile::select('id', 'user_id', 'conditions', 'created_at')
            ->whereNotNull('conditions')
            ->get();
        $conditionDistribution = [];
        foreach ($profiles as $profile) {
            $conditions = is_array($profile->conditions) ? $profile->conditions : json_decode($profile->conditions, true);
            if (is_array($conditions) && !empty($conditions) && isset($conditions[0]['value'])) {
                $condition = $conditions[0]['value'];
                $conditionDistribution[$condition] = ($conditionDistribution[$condition] ?? 0) + 1;
            }
        }

        // Contact analytics
        $totalContacts = Contact::count();
        $unresolvedContacts = Contact::where('status', 'pending')->count();
        $contacts = Contact::select('id', 'name', 'email', 'message', 'status', 'created_at')->get();

        // Correction requests analytics
        $totalCorrectionRequests = CorrectionRequest::count();
        $pendingCorrectionRequests = CorrectionRequest::where('status', 'pending')->count();
        $resolvedCorrectionRequests = CorrectionRequest::where('status', 'resolved')->count();
        $correctionRequests = CorrectionRequest::select('id', 'user_id', 'user_comments', 'status', 'created_at')->get();

        // Suggestions
        $suggestions = [
            'Send reminders to users with expired subscriptions.',
            'Offer discounts to inactive users.',
            'Analyze high-calorie meal trends for personalized recommendations.',
            'Promote vegan or gluten-free meal plans based on dietary trends.',
        ];

        return view('admin.analysis', compact(
            'totalUsers', 'activeUsers', 'suspendedUsers', 'users', 'signupTrend', 'loginTrend',
            'activeSubscriptions', 'trialSubscriptions', 'expiredSubscriptions', 'subscriptions',
            'mealTypeDistribution', 'calorieTrend', 'meals', 'dietaryDistribution', 'mealStatusDistribution',
            'totalCoupons', 'activeCoupons', 'usedCoupons', 'couponRedemptionRate', 'coupons',
            'conditionDistribution', 'profiles',
            'totalContacts', 'unresolvedContacts', 'contacts',
            'totalCorrectionRequests', 'pendingCorrectionRequests', 'resolvedCorrectionRequests', 'correctionRequests',
            'suggestions'
        ));
    }
 private function generateSuggestions($signupTrend, $loginTrend, $couponRedemptionRate, $activeSubscriptions)
    {
        $suggestions = [];
        
        // Analyze signup trend
        $signupValues = array_values($signupTrend);
        $avgSignups = array_sum($signupValues) / max(1, count($signupValues));
        $lastWeekSignups = end($signupValues);
        if ($lastWeekSignups < $avgSignups * 0.7) {
            $suggestions[] = __('Increase marketing efforts (e.g., social media campaigns, referral programs) to boost signups.');
        }
        if ($lastWeekSignups > $avgSignups * 1.3) {
            $suggestions[] = __('Maintain current marketing strategies as signups are above average.');
        }

        // Analyze login trend
        $loginValues = array_values($loginTrend);
        $avgLogins = array_sum($loginValues) / max(1, count($loginValues));
        $lastWeekLogins = end($loginValues);
        if ($lastWeekLogins < $avgLogins * 0.7) {
            $suggestions[] = __('Send push notifications or emails to re-engage inactive users.');
            $suggestions[] = __('Introduce gamification (e.g., streaks, rewards) to encourage daily logins.');
        }

        // Analyze coupon redemption
        if ($couponRedemptionRate < 30) {
            $suggestions[] = __('Promote coupons more aggressively via email or in-app notifications to increase redemption rates.');
        }

        // Analyze subscriptions
        if ($activeSubscriptions < $avgSignups * 0.5) {
            $suggestions[] = __('Offer limited-time discounts or extended trials to convert free users to subscribers.');
        }

        return $suggestions ?: [__('No specific suggestions at this time. Continue monitoring trends.')];
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function showMeal(Meal $meal)
    {
        return view('admin.meal.show', compact('meal'));
    }

    public function showUser(User $user)
    {
        return view('admin.meal.user', compact('user'));
    }

    public function showSubscription(Subscription $subscription)
    {
        return view('admin.meal.sub', compact('subscription'));
    }

    public function showCoupon(Coupon $coupon)
    {
        return view('admin.meal.cop', compact('coupon'));
    }

    public function showProfile(Profile $profile)
    {
        return view('admin.meal.profile', compact('profile'));
    }

    public function showContact(Contact $contact)
    {
        return view('admin.meal.contact', compact('contact'));
    }

   public function showCorrectionRequest(CorrectionRequest $correctionRequest)
{
    return view('admin.meal.correction', compact('correctionRequest'));
}

    
    
    
    
    
}