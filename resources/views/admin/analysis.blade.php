@extends('layouts.app2')

@section('title', __('Database Analysis'))

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 animate-fade-in">
    <h1 class="text-3xl font-bold text-nutri-blue dark:text-blue-400 mb-6">{{ __('Database Analysis') }}</h1>

    <!-- User Analytics -->
    <div x-data="{ open: false }" class="mb-12">
        <h2 class="text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4 flex justify-between items-center">
            {{ __('User Analytics') }}
            <button @click="open = !open" class="md:hidden text-nutri-blue dark:text-blue-400 focus:outline-none">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
        </h2>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Total Users') }}</h3>
                <p class="text-3xl text-nutri-blue dark:text-blue-400">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Active Users') }}</h3>
                <p class="text-3xl text-green-500">{{ $activeUsers }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Suspended Users') }}</h3>
                <p class="text-3xl text-red-500">{{ $suspendedUsers }}</p>
            </div>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Weekly Signups and Logins') }}</h3>
            <canvas id="signupLoginChart" class="w-full h-64"></canvas>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('User Details') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Last Login') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Joined') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ route('admin.user.show', $user) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->is_suspended ? __('Suspended') : __('Active') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : __('Never') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No users found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Subscription Analytics -->
    <div x-data="{ open: false }" class="mb-12">
        <h2 class="text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4 flex justify-between items-center">
            {{ __('Subscription Analytics') }}
            <button @click="open = !open" class="md:hidden text-nutri-blue dark:text-blue-400 focus:outline-none">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
        </h2>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Active Subscriptions') }}</h3>
                <p class="text-3xl text-green-500">{{ $activeSubscriptions }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Trial Subscriptions') }}</h3>
                <p class="text-3xl text-yellow-500">{{ $trialSubscriptions }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Expired Subscriptions') }}</h3>
                <p class="text-3xl text-red-500">{{ $expiredSubscriptions }}</p>
            </div>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Subscription Details') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('User ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Plan') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Started') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ route('admin.subscription.show', $subscription) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $subscription->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $subscription->user_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $subscription->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($subscription->stripe_status) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $subscription->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No subscriptions found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Meal Analytics -->
    <div x-data="{ open: false }" class="mb-12">
        <h2 class="text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4 flex justify-between items-center">
            {{ __('Meal Analytics') }}
            <button @click="open = !open" class="md:hidden text-nutri-blue dark:text-blue-400 focus:outline-none">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
        </h2>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Meal Type Distribution') }}</h3>
                <canvas id="mealTypeChart" class="w-full h-64"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Calorie Trend (Last 7 Days)') }}</h3>
                <canvas id="calorieTrendChart" class="w-full h-64"></canvas>
            </div>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Dietary Preferences') }}</h3>
                <canvas id="dietaryChart" class="w-full h-64"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Meal Status Distribution') }}</h3>
                <canvas id="mealStatusChart" class="w-full h-64"></canvas>
            </div>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Meal Details') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('User ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Type') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Calories') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($meals as $meal)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ route('admin.meal.show', $meal) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $meal->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $meal->user_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($meal->meal_type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $meal->calories }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($meal->status) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $meal->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No meals found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Coupon Analytics -->
    <div x-data="{ open: false }" class="mb-12">
        <h2 class="text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4 flex justify-between items-center">
            {{ __('Coupon Analytics') }}
            <button @click="open = !open" class="md:hidden text-nutri-blue dark:text-blue-400 focus:outline-none">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
        </h2>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Total Coupons') }}</h3>
                <p class="text-3xl text-nutri-blue dark:text-blue-400">{{ $totalCoupons }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Active Coupons') }}</h3>
                <p class="text-3xl text-green-500">{{ $activeCoupons }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Redemption Rate') }}</h3>
                <p class="text-3xl text-yellow-500">{{ number_format($couponRedemptionRate, 1) }}%</p>
            </div>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Coupon Status') }}</h3>
            <canvas id="couponChart" class="w-full h-64"></canvas>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Coupon Details') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Code') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Created') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($coupons as $coupon)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ route('admin.coupon.show', $coupon) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $coupon->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $coupon->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($coupon->status) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $coupon->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No coupons found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Profile Analytics -->
    <div x-data="{ open: false }" class="mb-12">
        <h2 class="text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4 flex justify-between items-center">
            {{ __('Profile Analytics') }}
            <button @click="open = !open" class="md:hidden text-nutri-blue dark:text-blue-400 focus:outline-none">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
        </h2>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Health Conditions') }}</h3>
            <canvas id="conditionChart" class="w-full h-64"></canvas>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Profile Details') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('User ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Condition') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Created') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($profiles as $profile)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ route('admin.profile.show', $profile) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $profile->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $profile->user_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ is_array($profile->conditions) && isset($profile->conditions[0]['value']) ? $profile->conditions[0]['value'] : __('None') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $profile->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No profiles found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Contact Analytics -->
    <div x-data="{ open: false }" class="mb-12">
        <h2 class="text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4 flex justify-between items-center">
            {{ __('Contact Analytics') }}
            <button @click="open = !open" class="md:hidden text-nutri-blue dark:text-blue-400 focus:outline-none">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
        </h2>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Total Contacts') }}</h3>
                <p class="text-3xl text-nutri-blue dark:text-blue-400">{{ $totalContacts }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Unresolved Contacts') }}</h3>
                <p class="text-3xl text-red-500">{{ $unresolvedContacts }}</p>
            </div>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Contact Details') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Message') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Created') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($contacts as $contact)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ route('admin.contact.show', $contact) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $contact->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $contact->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $contact->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($contact->message, 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($contact->status) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $contact->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No contacts found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Correction Requests Analytics -->
    <div x-data="{ open: false }" class="mb-12">
        <h2 class="text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4 flex justify-between items-center">
            {{ __('Correction Requests Analytics') }}
            <button @click="open = !open" class="md:hidden text-nutri-blue dark:text-blue-400 focus:outline-none">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
        </h2>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Total Requests') }}</h3>
                <p class="text-3xl text-nutri-blue dark:text-blue-400">{{ $totalCorrectionRequests }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Pending Requests') }}</h3>
                <p class="text-3xl text-yellow-500">{{ $pendingCorrectionRequests }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-all hover:shadow-lg">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Resolved Requests') }}</h3>
                <p class="text-3xl text-green-500">{{ $resolvedCorrectionRequests }}</p>
            </div>
        </div>
        <div x-show="open || window.innerWidth >= 768" x-transition x-cloak class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Correction Request Details') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('User ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Comments') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Created') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($correctionRequests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ route('admin.correction-request.show', $request) }}'">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $request->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $request->user_id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($request->user_comments, 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($request->status) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $request->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No correction requests found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Suggestions -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-nutri-blue dark:text-blue-400 mb-4">{{ __('Engagement Suggestions') }}</h2>
        <ul class="list-disc pl-5 space-y-2 text-gray-700 dark:text-gray-300">
            @forelse ($suggestions as $suggestion)
                <li>{{ $suggestion }}</li>
            @empty
                <li>{{ __('No suggestions available.') }}</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Ensure Chart.js plugins are registered
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart === 'undefined' || typeof ChartDataLabels === 'undefined' || typeof ChartZoom === 'undefined') {
            console.error('Chart.js or its plugins failed to load');
            return;
        }
        Chart.register(ChartDataLabels, ChartZoom);

        // Helper function to check if data is valid
        function isValidChartData(data, labels) {
            return Array.isArray(data) && data.length > 0 && Array.isArray(labels) && labels.length > 0 && data.some(value => value > 0);
        }

        // Signup and Login Trend Chart
        const signupTrendData = @json(array_values($signupTrend));
        const signupTrendLabels = @json(array_keys($signupTrend));
        const loginTrendData = @json(array_values($loginTrend));
        if (isValidChartData(signupTrendData, signupTrendLabels) || isValidChartData(loginTrendData, signupTrendLabels)) {
            new Chart(document.getElementById('signupLoginChart'), {
                type: 'line',
                data: {
                    labels: signupTrendLabels,
                    datasets: [
                        {
                            label: '{{ __('Signups') }}',
                            data: signupTrendData,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: '{{ __('Logins') }}',
                            data: loginTrendData,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        zoom: {
                            zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' }
                        },
                        datalabels: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: '{{ __('Count') }}' } },
                        x: { title: { display: true, text: '{{ __('Date') }}' } }
                    }
                }
            });
        } else {
            console.warn('No valid data for signupLoginChart');
            document.getElementById('signupLoginChart').parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">{{ __('No data available for this chart.') }}</p>';
        }

        // Meal Type Distribution Chart
        const mealTypeData = @json(array_values($mealTypeDistribution));
        const mealTypeLabels = ['{{ __('Breakfast') }}', '{{ __('Lunch') }}', '{{ __('Dinner') }}'];
        if (isValidChartData(mealTypeData, mealTypeLabels)) {
            new Chart(document.getElementById('mealTypeChart'), {
                type: 'pie',
                data: {
                    labels: mealTypeLabels,
                    datasets: [{
                        data: mealTypeData,
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold' },
                            formatter: (value, ctx) => value > 0 ? value : ''
                        }
                    }
                }
            });
        } else {
            console.warn('No valid data for mealTypeChart');
            document.getElementById('mealTypeChart').parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">{{ __('No data available for this chart.') }}</p>';
        }

        // Calorie Trend Chart
        const calorieTrendData = @json(array_values($calorieTrend));
        const calorieTrendLabels = @json(array_keys($calorieTrend));
        if (isValidChartData(calorieTrendData, calorieTrendLabels)) {
            new Chart(document.getElementById('calorieTrendChart'), {
                type: 'bar',
                data: {
                    labels: calorieTrendLabels,
                    datasets: [{
                        label: '{{ __('Calories') }}',
                        data: calorieTrendData,
                        backgroundColor: '#3B82F6',
                        borderColor: '#1F2937',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        datalabels: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: '{{ __('Calories (kcal)') }}' } },
                        x: { title: { display: true, text: '{{ __('Date') }}' } }
                    }
                }
            });
        } else {
            console.warn('No valid data for calorieTrendChart');
            document.getElementById('calorieTrendChart').parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">{{ __('No data available for this chart.') }}</p>';
        }

        // Dietary Distribution Chart
        const dietaryData = @json(array_values($dietaryDistribution));
        const dietaryLabels = @json(array_keys($dietaryDistribution));
        if (isValidChartData(dietaryData, dietaryLabels)) {
            new Chart(document.getElementById('dietaryChart'), {
                type: 'pie',
                data: {
                    labels: dietaryLabels,
                    datasets: [{
                        data: dietaryData,
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold' },
                            formatter: (value, ctx) => value > 0 ? value : ''
                        }
                    }
                }
            });
        } else {
            console.warn('No valid data for dietaryChart');
            document.getElementById('dietaryChart').parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">{{ __('No data available for this chart.') }}</p>';
        }

        // Meal Status Distribution Chart
        const mealStatusData = @json(array_values($mealStatusDistribution));
        const mealStatusLabels = @json(array_keys($mealStatusDistribution));
        if (isValidChartData(mealStatusData, mealStatusLabels)) {
            new Chart(document.getElementById('mealStatusChart'), {
                type: 'pie',
                data: {
                    labels: mealStatusLabels,
                    datasets: [{
                        data: mealStatusData,
                        backgroundColor: ['#F59E0B', '#10B981'],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold' },
                            formatter: (value, ctx) => value > 0 ? value : ''
                        }
                    }
                }
            });
        } else {
            console.warn('No valid data for mealStatusChart');
            document.getElementById('mealStatusChart').parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">{{ __('No data available for this chart.') }}</p>';
        }

        // Coupon Chart
        const couponData = [@json($activeCoupons), @json($usedCoupons)];
        const couponLabels = ['{{ __('Active') }}', '{{ __('Used') }}'];
        if (isValidChartData(couponData, couponLabels)) {
            new Chart(document.getElementById('couponChart'), {
                type: 'doughnut',
                data: {
                    labels: couponLabels,
                    datasets: [{
                        data: couponData,
                        backgroundColor: ['#10B981', '#F59E0B'],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold' },
                            formatter: (value, ctx) => value > 0 ? value : ''
                        }
                    }
                }
            });
        } else {
            console.warn('No valid data for couponChart');
            document.getElementById('couponChart').parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">{{ __('No data available for this chart.') }}</p>';
        }

        // Condition Distribution Chart
        const conditionData = @json(array_values($conditionDistribution));
        const conditionLabels = @json(array_keys($conditionDistribution));
        if (isValidChartData(conditionData, conditionLabels)) {
            new Chart(document.getElementById('conditionChart'), {
                type: 'bar',
                data: {
                    labels: conditionLabels,
                    datasets: [{
                        label: '{{ __('Users with Condition') }}',
                        data: conditionData,
                        backgroundColor: '#3B82F6',
                        borderColor: '#1F2937',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        datalabels: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: '{{ __('Count') }}' } },
                        x: { title: { display: true, text: '{{ __('Condition') }}' } }
                    }
                }
            });
        } else {
            console.warn('No valid data for conditionChart');
            document.getElementById('conditionChart').parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">{{ __('No data available for this chart.') }}</p>';
        }
    });
</script>
@endsection