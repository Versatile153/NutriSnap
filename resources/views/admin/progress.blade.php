
@extends('layouts.app1')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $title }}</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-300">
                @if (isset($error))
                    {{ $error }}
                @else
                    {{ __('Review :name\'s meal compliance and progress metrics.', ['name' => $user->name]) }}
                @endif
            </p>
        </div>

        <div class="mb-8 flex flex-wrap gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">{{ __('Back to Dashboard') }}</a>
        </div>

        @if (!isset($error))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('Profile Summary') }}</h3>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Daily Calorie Goal') }}: {{ $dailyCalorieGoal ? $dailyCalorieGoal . ' kcal' : __('Not set') }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Health Conditions') }}: {{ implode(', ', $profile->conditions ?? ['None']) }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Current Weight') }}: {{ is_numeric($profile->weight) ? $profile->weight . ' kg' : __('Not set') }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Target Weight') }}: {{ is_numeric($profile->target_weight) ? $profile->target_weight . ' kg' : __('Not set') }}</p>
                <p class="text-gray-600 dark:text-gray-300 {{ $goalAchieved === 'Achieved' ? 'text-green-600 dark:text-green-400' : ($goalAchieved === 'Not Achieved' ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300') }}">
                    {{ __('Goal Status') }}: {{ __($goalAchieved) }}
                </p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Weight Change') }}: {{ $weightChange >= 0 ? '+' : '' }}{{ $weightChange }} kg</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Days Active') }}: {{ $daysActive }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Active Coupons') }}: {{ $activeCoupons }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Pending Correction Requests') }}: {{ $correctionRequests }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('Today\'s Meals') }}</h3>
                @if ($meals->isEmpty())
                    <p class="text-gray-600 dark:text-gray-300">{{ __('No meals recorded today.') }}</p>
                @else
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-gray-700 dark:text-gray-200">
                                <th class="p-2">{{ __('Meal Type') }}</th>
                                <th class="p-2">{{ __('Food') }}</th>
                                <th class="p-2">{{ __('Calories') }}</th>
                                <th class="p-2">{{ __('Time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($meals as $meal)
                                <tr class="border-t border-gray-200 dark:border-gray-600">
                                    <td class="p-2">{{ $meal->meal_type ?? 'Unknown' }}</td>
                                    <td class="p-2">{{ $meal->analysis['food'] ?? 'Unknown' }}</td>
                                    <td class="p-2">{{ $meal->calories ?? 0 }} kcal</td>
                                    <td class="p-2">{{ $meal->created_at->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('Compliance Summary') }}</h3>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Total Calories Today') }}: {{ $totalCalories }} kcal ({{ __('Goal') }}: {{ $dailyCalorieGoal ? $dailyCalorieGoal . ' kcal' : __('Not set') }})</p>
                <p class="text-gray-600 dark:text-gray-300 {{ $calorieCompliance === 'Compliant' ? 'text-green-600 dark:text-green-400' : ($calorieCompliance === 'Over Limit' ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300') }}">
                    {{ __('Calorie Compliance') }}: {{ __($calorieCompliance) }}
                </p>
                <p class="text-gray-600 dark:text-gray-300 {{ $dietaryCompliance === 'Compliant' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ __('Dietary Compliance') }}: {{ __($dietaryCompliance) }}
                </p>
                @if (!empty($nonCompliantMeals))
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mt-4">{{ __('Non-Compliant Meals') }}</h4>
                    <ul class="list-disc pl-5 text-gray-600 dark:text-gray-300">
                        @foreach ($nonCompliantMeals as $nonCompliant)
                            <li>{{ __('Meal :meal_id (:food) violates :condition.', ['meal_id' => $nonCompliant['meal_id'], 'food' => $nonCompliant['food'], 'condition' => $nonCompliant['condition']]) }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('Progress Metrics') }}</h3>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Macronutrient Trends') }}</h4>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Protein') }}: {{ $macroTrends['protein'] ? $macroTrends['protein'] . ' g' : __('Not available') }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Carbohydrates') }}: {{ $macroTrends['carbs'] ? $macroTrends['carbs'] . ' g' : __('Not available') }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Fat') }}: {{ $macroTrends['fat'] ? $macroTrends['fat'] . ' g' : __('Not available') }}</p>

                <h4 class="text-lg font-medium text-gray-900 dark:text-white mt-4">{{ __('Meal Type Distribution') }}</h4>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Breakfast') }}: {{ $breakfastMeals }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Lunch') }}: {{ $lunchMeals }}</p>
                <p class="text-gray-600 dark:text-gray-300">{{ __('Dinner') }}: {{ $dinnerMeals }}</p>

                <h4 class="text-lg font-medium text-gray-900 dark:text-white mt-4">{{ __('Calorie Trend (Last 7 Days)') }}</h4>
                <canvas id="calorieTrendChart" class="mt-4"></canvas>
            </div>
        @endif

      

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if (!isset($error))
            const ctx = document.getElementById('calorieTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json(array_keys($calorieTrend)),
                    datasets: [{
                        label: '{{ __('Daily Calories') }}',
                        data: @json(array_values($calorieTrend)),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: '{{ __('Calories (kcal)') }}' } },
                        x: { title: { display: true, text: '{{ __('Date') }}' } }
                    }
                }
            });
        @endif
    </script>
@endsection
