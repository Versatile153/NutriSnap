@extends('layouts.app1')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('status') || session('error'))
            Swal.fire({
                icon: '{{ session('error') ? 'error' : 'success' }}',
                title: '{{ session('error') ? __('Error') : __('Success') }}',
                text: '{{ session('error') ?: session('status') }}',
                timer: 3000,
                showConfirmButton: false,
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#ec4899',
            });
        @endif
    </script>

    <main class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 mt-8 pb-20">
        <h2 class="text-[22px] leading-7 font-semibold text-white mb-6">
            {{ __('Welcome') }}, {{ ucfirst($user->name) }} - {{ now()->format('h:i A T, l, F j, Y') }}
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Profile Details -->
            <section class="lg:col-span-2 bg-gray-800 card rounded-lg p-6 shadow-md transition-all hover:shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[18px] font-semibold text-white">{{ __('Your Profile Details') }}</h3>
                    <a href="{{ route('settings') }}" class="text-[13px] text-pink-400 hover:text-pink-300 transition-colors">{{ __('Edit Profile') }}</a>
                </div>
                <p class="text-[12px] text-gray-400 mb-5">{{ __('Your personal health information') }}</p>
                @if ($profile)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="kpi rounded-lg p-5 bg-gray-700 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Height') }}</p>
                            <p class="text-pink-400 text-[18px] font-extrabold">{{ $profile->height }} cm</p>
                        </div>
                        <div class="kpi rounded-lg p-5 bg-gray-700 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Weight') }}</p>
                            <p class="text-pink-400 text-[18px] font-extrabold">{{ $profile->weight }} kg</p>
                        </div>
                        <div class="kpi rounded-lg p-5 bg-gray-700 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Goal') }}</p>
                            <p class="text-pink-400 text-[18px] font-extrabold">{{ ucfirst(str_replace('_', ' ', $profile->goal)) }}</p>
                        </div>
                        <div class="kpi rounded-lg p-5 bg-gray-700 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Daily Calories') }}</p>
                            <p class="text-pink-400 text-[18px] font-extrabold">{{ $profile->daily_calories ?? 0 }} kcal</p>
                        </div>
                        <div class="kpi rounded-lg p-5 bg-gray-700 sm:col-span-2 lg:col-span-3 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Health Conditions') }}</p>
                            <p class="text-pink-400 text-[18px] font-extrabold">
                                @if (is_array($profile->health_conditions) && !empty($profile->health_conditions))
                                    {{ implode(', ', array_map('ucfirst', $profile->health_conditions)) }}
                                @elseif (is_string($profile->health_conditions) && !empty($profile->health_conditions))
                                    {{ ucfirst($profile->health_conditions) }}
                                @else
                                    {{ __('None') }}
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-[13px] text-gray-400">{{ __('No profile details available.') }} <a href="{{ route('settings') }}" class="text-pink-400 hover:text-pink-300 underline transition-colors">{{ __('Complete your profile') }}</a>.</p>
                @endif
            </section>

            <!-- Macronutrient Overview -->
            <section class="lg:col-span-2 bg-gray-800 card rounded-lg p-6 shadow-md transition-all hover:shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[18px] font-semibold text-white">{{ __('Meal Analysis Overview') }}</h3>
                    <a href="{{ route('meals.index') }}" class="text-[13px] text-pink-400 hover:text-pink-300 transition-colors">{{ __('View Details') }}</a>
                </div>
                <p class="text-[13px] text-gray-400 mb-5">{{ __('Tracking your intake for :period', ['period' => $period === '1week' ? __('Last 7 Days') : ($period === '3months' ? __('Last 3 Months') : __('Last 30 Days'))]) }}</p>

                <div class="flex items-center justify-end gap-2 mb-4">
                    <a href="{{ route('dashboard', ['period' => '1week']) }}" class="pill {{ $period === '1week' ? 'pill-active' : '' }}">{{ __('1 Week') }}</a>
                    <a href="{{ route('dashboard', ['period' => '1month']) }}" class="pill {{ $period === '1month' ? 'pill-active' : '' }}">{{ __('1 Month') }}</a>
                    <a href="{{ route('dashboard', ['period' => '3months']) }}" class="pill {{ $period === '3months' ? 'pill-active' : '' }}">{{ __('3 Months') }}</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Macronutrient Bar Chart -->
                    <div class="border border-gray-700 rounded-lg p-4 bg-gray-900 @if($macroTrends->isEmpty()) hidden @endif">
                        <canvas id="macroChart" class="w-full" style="height: 300px;"></canvas>
                    </div>
                    <!-- Meal Type Pie Chart -->
                    <div class="border border-gray-700 rounded-lg p-4 bg-gray-900 @if($mealTypeDistribution['breakfast'] == 0 && $mealTypeDistribution['lunch'] == 0 && $mealTypeDistribution['dinner'] == 0) hidden @endif">
                        <canvas id="mealTypeChart" class="w-full" style="height: 300px;"></canvas>
                    </div>
                    <!-- Calorie Trend Line Chart -->
                    <div class="border border-gray-700 rounded-lg p-4 bg-gray-900 @if($calorieTrend->isEmpty()) hidden @endif">
                        <canvas id="calorieTrendChart" class="w-full" style="height: 300px;"></canvas>
                    </div>
                    <!-- KPI Metrics -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="kpi rounded-lg p-5 bg-gray-700 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Total Intake') }} <span class="text-pink-400">
                                @php
                                    $totalDays = $period === '1week' ? 7 : ($period === '3months' ? 90 : 30);
                                    $denominator = ($profile->daily_calories ?? 0) * $totalDays;
                                    $percentageChange = $denominator > 0 ? round(($totalIntake - $denominator) / $denominator * 100, 1) : 0;
                                @endphp
                                {{ $percentageChange > 0 ? '+' . $percentageChange . '%' : ($percentageChange < 0 ? $percentageChange . '%' : '0%') }}
                            </span></p>
                            <p class="text-pink-400 text-[18px] font-extrabold">{{ round($totalIntake) }} kcal</p>
                        </div>
                        <div class="kpi rounded-lg p-5 bg-gray-700 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Daily Avg') }} <span class="text-pink-400">
                                @php
                                    $dailyChange = ($profile->daily_calories ?? 0) > 0 ? round(($dailyAvg - ($profile->daily_calories ?? 0)) / ($profile->daily_calories ?? 0) * 100, 1) : 0;
                                @endphp
                                {{ $dailyChange > 0 ? '+' . $dailyChange . '%' : ($dailyChange < 0 ? $dailyChange . '%' : '0%') }}
                            </span></p>
                            <p class="text-pink-400 text-[18px] font-extrabold">{{ round($dailyAvg) }} kcal</p>
                        </div>
                        <div class="kpi rounded-lg p-5 bg-gray-700 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Goal Progress') }} <span class="text-pink-400">
                                @php
                                    $progressChange = $goalProgress > 0 ? round($goalProgress - 100, 1) : 0;
                                @endphp
                                {{ $progressChange > 0 ? '+' . $progressChange . '%' : ($progressChange < 0 ? $progressChange . '%' : '0%') }}
                            </span></p>
                            <p class="text-pink-400 text-[18px] font-extrabold">{{ round($goalProgress) }}%</p>
                        </div>
                        <div class="kpi rounded-lg p-5 bg-gray-700 flex flex-col justify-between transition-all hover:bg-gray-600">
                            <p class="text-[12px] text-gray-400">{{ __('Calories Burned') }} <span class="text-pink-400">
                                @php
                                    $burnedPercentage = $totalIntake > 0 ? round($caloriesBurned / $totalIntake * 100, 1) : 0;
                                @endphp
                                {{ $burnedPercentage > 0 ? '-' . $burnedPercentage . '%' : '0%' }}
                            </span></p>
                            <p class="text-pink-400 text-[18px] font-extrabold">{{ round($caloriesBurned) }} kcal</p>
                        </div>
                    </div>
                </div>
                @if($macroTrends->isEmpty() && $mealTypeDistribution['breakfast'] == 0 && $mealTypeDistribution['lunch'] == 0 && $mealTypeDistribution['dinner'] == 0 && $calorieTrend->isEmpty())
                    <p class="text-[13px] text-gray-400 text-center mt-4">{{ __('No meal analysis data available for the selected period.') }}</p>
                @endif
            </section>

            <!-- Meal & User Overview -->
            <aside class="bg-gray-800 card rounded-lg p-6 shadow-md transition-all hover:shadow-lg flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-pink-400/20 p-2 text-pink-400"><i data-feather="list" class="w-4 h-4"></i></span>
                            <h4 class="font-semibold text-[16px] text-white">{{ __('Meal Logs Overview') }}</h4>
                        </div>
                        <a href="{{ route('meals.index') }}" class="text-[13px] text-pink-400 hover:text-pink-300 transition-colors">{{ __('View all') }}</a>
                    </div>
                    <div class="grid grid-cols-3 text-center mb-6">
                        <div>
                            <p class="text-[18px] font-semibold text-white">{{ $totalMeals }}</p>
                            <p class="text-[12px] text-gray-400">{{ __('Total Meals') }}</p>
                        </div>
                        <div>
                            <p class="text-[18px] font-semibold text-white">{{ $mealsToday }}</p>
                            <p class="text-[12px] text-gray-400">{{ __('Logged Today') }}</p>
                        </div>
                        <div>
                            <p class="text-[18px] font-semibold text-white">{{ $pendingMeals }}</p>
                            <p class="text-[12px] text-gray-400">{{ __('Pending Review') }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-pink-400/20 p-2 text-pink-400"><i data-feather="edit" class="w-4 h-4"></i></span>
                            <h4 class="font-semibold text-[16px] text-white">{{ __('Correction Requests') }}</h4>
                        </div>
                        <a href="{{ route('meals.index') }}" class="text-[13px] text-pink-400 hover:text-pink-300 transition-colors">{{ __('View all') }}</a>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mb-6 max-h-[150px] overflow-y-auto">
                        @if ($correctionRequests->isEmpty())
                            <p class="text-[13px] text-gray-400">{{ __('No correction requests submitted.') }}</p>
                        @else
                            @foreach ($correctionRequests as $request)
                                <div class="text-[13px] text-gray-400">
                                    <p class="text-white">{{ __('Meal') }} #{{ $request->meal_id }} ({{ ucfirst($request->status) }})</p>
                                    <p>{{ __('Comments') }}: {{ Str::limit($request->user_comments, 50) }}</p>
                                    <p>{{ __('Submitted') }}: {{ $request->created_at->format('M d, Y') }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-pink-400/20 p-2 text-pink-400"><i data-feather="user" class="w-4 h-4"></i></span>
                            <h4 class="font-semibold text-[16px] text-white">{{ __('User Progress') }}</h4>
                        </div>
                      <a href="{{ route('progress.index', ['user_id' => $user->id]) }}" class="text-[13px] text-pink-400 hover:text-pink-300 transition-colors">{{ __('View Progress') }}</a>
                    </div>
                    <div class="grid grid-cols-3 text-center mb-6">
                        <div>
                            <p class="text-[18px] font-semibold text-white">{{ $goalAchieved }}%</p>
                            <p class="text-[12px] text-gray-400">{{ __('Goal Achieved') }}</p>
                        </div>
                        <div>
                            <p class="text-[18px] font-semibold text-white">{{ $daysActive }}</p>
                            <p class="text-[12px] text-gray-400">{{ __('Days Active') }}</p>
                        </div>
                        <div>
                            <p class="text-[18px] font-semibold text-white">{{ abs($weightChange) }} kg</p>
                            <p class="text-[12px] text-gray-400">{{ $weightChange >= 0 ? __('Weight Gained') : __('Weight Lost') }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-pink-400/20 p-2 text-pink-400"><i data-feather="tag" class="w-4 h-4"></i></span>
                            <h4 class="font-semibold text-[16px] text-white">{{ __('Active Coupons') }}</h4>
                        </div>
                        <a href="{{ route('coupons.index') }}" class="text-[13px] text-pink-400 hover:text-pink-300 transition-colors">{{ __('View all') }}</a>
                    </div>
                    <div class="grid grid-cols-1 gap-4 max-h-[150px] overflow-y-auto">
                        @if ($activeCoupons->isEmpty())
                            <p class="text-[13px] text-gray-400">{{ __('No active coupons available.') }}</p>
                        @else
                            @foreach ($activeCoupons as $coupon)
                                <div class="text-[13px] text-gray-400">
                                    <p class="text-white">{{ $coupon->code }} ({{ $coupon->discount_percentage }}% {{ __('off') }})</p>
                                    <p>{{ __('Expires') }}: {{ $coupon->expires_at->format('M d, Y') }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </aside>
        </div>

        <!-- Meal Sliders -->
        @if ($breakfastMeals->isNotEmpty() || $lunchMeals->isNotEmpty() || $dinnerMeals->isNotEmpty())
            <section class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Slider 1: Breakfast -->
                @if ($breakfastMeals->isNotEmpty())
                    <article class="relative rounded-xl overflow-hidden card h-[350px] w-full slider shadow-md transition-all hover:shadow-lg">
                        <div class="absolute top-2 left-2 text-white bg-pink-400/70 px-2 py-1 rounded text-sm z-10">{{ __('Breakfast') }}</div>
                        <div class="slider-track h-full">
                            @foreach ($breakfastMeals as $photo)
                                <img src="{{ $photo ? Storage::url($photo) : asset('images/placeholder.jpg') }}" alt="{{ __('Breakfast Meal') }}" class="object-cover w-full h-full" onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                            @endforeach
                        </div>
                        <div class="dots absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-2 z-10"></div>
                    </article>
                @else
                    <article class="relative rounded-xl overflow-hidden card h-[350px] w-full shadow-md transition-all hover:shadow-lg flex items-center justify-center">
                        <p class="text-[13px] text-gray-400 text-center">{{ __('No breakfast meals uploaded.') }} <a href="{{ route('meals.index') }}" class="text-pink-400 hover:text-pink-300 underline transition-colors">{{ __('Upload now!') }}</a></p>
                    </article>
                @endif

                <!-- Slider 2: Lunch -->
                @if ($lunchMeals->isNotEmpty())
                    <article class="relative rounded-xl overflow-hidden card h-[350px] w-full slider shadow-md transition-all hover:shadow-lg">
                        <div class="absolute top-2 left-2 text-white bg-pink-400/70 px-2 py-1 rounded text-sm z-10">{{ __('Lunch') }}</div>
                        <div class="slider-track h-full">
                            @foreach ($lunchMeals as $photo)
                                <img src="{{ $photo ? Storage::url($photo) : asset('images/placeholder.jpg') }}" alt="{{ __('Lunch Meal') }}" class="object-cover w-full h-full" onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                            @endforeach
                        </div>
                        <div class="dots absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-2 z-10"></div>
                    </article>
                @else
                    <article class="relative rounded-xl overflow-hidden card h-[350px] w-full shadow-md transition-all hover:shadow-lg flex items-center justify-center">
                        <p class="text-[13px] text-gray-400 text-center">{{ __('No lunch meals uploaded.') }} <a href="{{ route('meals.index') }}" class="text-pink-400 hover:text-pink-300 underline transition-colors">{{ __('Upload now!') }}</a></p>
                    </article>
                @endif

                <!-- Slider 3: Dinner -->
                @if ($dinnerMeals->isNotEmpty())
                    <article class="relative rounded-xl overflow-hidden card h-[350px] w-full slider shadow-md transition-all hover:shadow-lg">
                        <div class="absolute top-2 left-2 text-white bg-pink-400/70 px-2 py-1 rounded text-sm z-10">{{ __('Dinner') }}</div>
                        <div class="slider-track h-full">
                            @foreach ($dinnerMeals as $photo)
                                <img src="{{ $photo ? Storage::url($photo) : asset('images/placeholder.jpg') }}" alt="{{ __('Dinner Meal') }}" class="object-cover w-full h-full" onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                            @endforeach
                        </div>
                        <div class="dots absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-2 z-10"></div>
                    </article>
                @else
                    <article class="relative rounded-xl overflow-hidden card h-[350px] w-full shadow-md transition-all hover:shadow-lg flex items-center justify-center">
                        <p class="text-[13px] text-gray-400 text-center">{{ __('No dinner meals uploaded.') }} <a href="{{ route('meals.index') }}" class="text-pink-400 hover:text-pink-300 underline transition-colors">{{ __('Upload now!') }}</a></p>
                    </article>
                @endif
            </section>
        @else
            <section class="mt-8 bg-gray-800 card rounded-lg p-6 shadow-md transition-all hover:shadow-lg">
                <p class="text-[13px] text-gray-400 text-center">{{ __('No meals uploaded yet.') }} <a href="{{ route('meals.index') }}" class="text-pink-400 hover:text-pink-300 underline transition-colors">{{ __('Upload your first meal now!') }}</a></p>
            </section>
        @endif
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        // Pass translations to JavaScript
        const translations = {
            meal_type: '{{ __('Meal Type') }}',
            macronutrients_grams: '{{ __('Macronutrients (grams)') }}',
            protein: '{{ __('Protein (g)') }}',
            carbohydrates: '{{ __('Carbohydrates (g)') }}',
            fat: '{{ __('Fat (g)') }}',
            breakfast: '{{ __('Breakfast') }}',
            lunch: '{{ __('Lunch') }}',
            dinner: '{{ __('Dinner') }}',
            date: '{{ __('Date') }}',
            calories_kcal: '{{ __('Calories (kcal)') }}',
            daily_calorie_intake: '{{ __('Daily Calorie Intake (kcal)') }}'
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Feather Icons
            feather.replace();

            // Macronutrient Bar Chart (by meal type)
            const macroChartCtx = document.getElementById('macroChart')?.getContext('2d');
            if (macroChartCtx && !@json($macroTrends->isEmpty())) {
                new Chart(macroChartCtx, {
                    type: 'bar',
                    data: {
                        labels: [translations.breakfast, translations.lunch, translations.dinner],
                        datasets: [
                            {
                                label: translations.protein,
                                data: [
                                    @json($macroTrends->where('meal_type', 'breakfast')->first()->protein ?? 0),
                                    @json($macroTrends->where('meal_type', 'lunch')->first()->protein ?? 0),
                                    @json($macroTrends->where('meal_type', 'dinner')->first()->protein ?? 0)
                                ],
                                backgroundColor: '#ec4899',
                                stack: 'Stack 0',
                            },
                            {
                                label: translations.carbohydrates,
                                data: [
                                    @json($macroTrends->where('meal_type', 'breakfast')->first()->carbs ?? 0),
                                    @json($macroTrends->where('meal_type', 'lunch')->first()->carbs ?? 0),
                                    @json($macroTrends->where('meal_type', 'dinner')->first()->carbs ?? 0)
                                ],
                                backgroundColor: '#8b5cf6',
                                stack: 'Stack 0',
                            },
                            {
                                label: translations.fat,
                                data: [
                                    @json($macroTrends->where('meal_type', 'breakfast')->first()->fat ?? 0),
                                    @json($macroTrends->where('meal_type', 'lunch')->first()->fat ?? 0),
                                    @json($macroTrends->where('meal_type', 'dinner')->first()->fat ?? 0)
                                ],
                                backgroundColor: '#3b82f6',
                                stack: 'Stack 0',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: { display: true, text: translations.meal_type, color: '#D1D5DB', font: { size: 14 } },
                                ticks: { color: '#D1D5DB' },
                                grid: { display: false },
                                stacked: true,
                            },
                            y: {
                                title: { display: true, text: translations.macronutrients_grams, color: '#D1D5DB', font: { size: 14 } },
                                ticks: { color: '#D1D5DB', stepSize: 50 },
                                grid: { color: 'rgba(75, 85, 99, 0.2)' },
                                beginAtZero: true,
                                stacked: true,
                            },
                        },
                        plugins: {
                            legend: { labels: { color: '#D1D5DB', font: { size: 14 } } },
                            tooltip: { 
                                enabled: true, 
                                backgroundColor: '#1f2937', 
                                titleColor: '#f3f4f6', 
                                bodyColor: '#d1d5db',
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw}g`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.log('No macronutrient data to display');
            }

            // Meal Type Pie Chart
            const mealTypeChartCtx = document.getElementById('mealTypeChart')?.getContext('2d');
            if (mealTypeChartCtx && !(@json($mealTypeDistribution['breakfast'] == 0 && $mealTypeDistribution['lunch'] == 0 && $mealTypeDistribution['dinner'] == 0))) {
                new Chart(mealTypeChartCtx, {
                    type: 'pie',
                    data: {
                        labels: [translations.breakfast, translations.lunch, translations.dinner],
                        datasets: [{
                            data: [
                                @json($mealTypeDistribution['breakfast'] ?? 0),
                                @json($mealTypeDistribution['lunch'] ?? 0),
                                @json($mealTypeDistribution['dinner'] ?? 0)
                            ],
                            backgroundColor: ['#ec4899', '#8b5cf6', '#3b82f6'],
                            borderColor: '#1f2937',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                position: 'bottom',
                                labels: { color: '#D1D5DB', font: { size: 14 } }
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: '#1f2937',
                                titleColor: '#f3f4f6',
                                bodyColor: '#d1d5db',
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                        const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                        return `${context.label}: ${context.raw} meals (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.log('No meal type distribution data to display');
            }

            // Calorie Trend Line Chart
            const calorieTrendChartCtx = document.getElementById('calorieTrendChart')?.getContext('2d');
            if (calorieTrendChartCtx && !@json($calorieTrend->isEmpty())) {
                new Chart(calorieTrendChartCtx, {
                    type: 'line',
                    data: {
                        labels: [@foreach ($calorieTrend as $trend)'{{ $trend->date }}', @endforeach],
                        datasets: [{
                            label: translations.daily_calorie_intake,
                            data: [@foreach ($calorieTrend as $trend){{ $trend->total }}, @endforeach],
                            borderColor: '#ec4899',
                            backgroundColor: 'rgba(236, 72, 153, 0.2)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ec4899',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#ec4899'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: { display: true, text: translations.date, color: '#D1D5DB', font: { size: 14 } },
                                ticks: { color: '#D1D5DB', maxTicksLimit: 10 },
                                grid: { display: false }
                            },
                            y: {
                                title: { display: true, text: translations.calories_kcal, color: '#D1D5DB', font: { size: 14 } },
                                ticks: { color: '#D1D5DB', stepSize: 200 },
                                grid: { color: 'rgba(75, 85, 99, 0.2)' },
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: { labels: { color: '#D1D5DB', font: { size: 14 } } },
                            tooltip: {
                                enabled: true,
                                backgroundColor: '#1f2937',
                                titleColor: '#f3f4f6',
                                bodyColor: '#d1d5db',
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw} kcal`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.log('No calorie trend data to display');
            }

            // SweetAlert for specific messages
            @if (session('status') === 'profile-created')
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('Profile Created!') }}',
                    text: '{{ __('Your NutriSnap profile has been successfully created.') }}',
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#ec4899',
                    timer: 3000,
                    showConfirmButton: false,
                });
            @elseif (session('status') === 'profile-updated')
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('Profile Updated!') }}',
                    text: '{{ __('Your profile information has been successfully updated.') }}',
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#ec4899',
                    timer: 3000,
                    showConfirmButton: false,
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Error') }}',
                    text: '{{ session('error') }}',
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#ec4899',
                    timer: 3000,
                    showConfirmButton: false,
                });
            @endif

            // Slider functionality
            document.querySelectorAll('.slider').forEach(slider => {
                const track = slider.querySelector('.slider-track');
                const images = track.querySelectorAll('img');
                const dotsContainer = slider.querySelector('.dots');
                let currentIndex = 0;

                // Create dots
                images.forEach((_, index) => {
                    const dot = document.createElement('span');
                    dot.classList.add('dot', 'w-2', 'h-2', 'rounded-full', 'bg-gray-400', 'inline-block', 'mx-1', 'cursor-pointer', 'transition-all');
                    if (index === 0) dot.classList.add('bg-pink-400');
                    dot.addEventListener('click', () => {
                        currentIndex = index;
                        updateSlider();
                    });
                    dotsContainer.appendChild(dot);
                });

                function updateSlider() {
                    track.style.transform = `translateX(-${currentIndex * 100}%)`;
                    dotsContainer.querySelectorAll('.dot').forEach((dot, index) => {
                        dot.classList.toggle('bg-pink-400', index === currentIndex);
                        dot.classList.toggle('bg-gray-400', index !== currentIndex);
                    });
                }

                function autoSlide() {
                    currentIndex = (currentIndex + 1) % images.length;
                    updateSlider();
                }

                setInterval(autoSlide, 3000); // Auto-slide every 3 seconds
                updateSlider();
            });
        });
        
         document.addEventListener('DOMContentLoaded', () => {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                setTimeout(() => {
                    preloader.classList.add('hidden');
                }, 1000);
            } else {
                console.error('Preloader element not found');
            }
        });
    </script>

    <style>
        .slider {
            position: relative;
            overflow: hidden;
            height: 350px;
            width: 100%;
        }
        .slider-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
            width: 100%;
            height: 100%;
        }
        .slider-track img {
            flex-shrink: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .dots {
            position: absolute;
            bottom: 10px;
            z-index: 10;
        }
        .dot:hover {
            transform: scale(1.2);
        }
        .card {
            background-color: #1f2937;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        .kpi {
            background-color: #374151;
            transition: all 0.3s ease;
        }
        .pill {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            background-color: #374151;
            color: #D1D5DB;
            font-size: 0.75rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .pill-active {
            background-color: #EC4899;
            color: #FFFFFF;
        }
        .max-h-[150px]::-webkit-scrollbar {
            width: 6px;
        }
        .max-h-[150px]::-webkit-scrollbar-track {
            background: #1f2937;
        }
        .max-h-[150px]::-webkit-scrollbar-thumb {
            background: #EC4899;
            border-radius: 3px;
        }
        .max-h-[150px]::-webkit-scrollbar-thumb:hover {
            background: #f472b6;
        }
    </style>
@endsection