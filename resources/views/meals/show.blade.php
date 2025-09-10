@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto bg-gray-900/95 backdrop-blur-md p-6 sm:p-8 lg:p-12 rounded-3xl shadow-2xl animate-slideUp mt-10">
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-80 flex items-center justify-center z-50 hidden" aria-busy="true" aria-label="{{ __('Processing request') }}">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-12 h-12 border-4 border-t-transparent border-pink-500 rounded-full animate-spin" role="status"></div>
                <p class="text-white text-lg font-medium">{{ __('Processing request') }}</p>
            </div>
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-400 via-purple-400 to-indigo-400 tracking-tight">{{ __('Meal Details') }}</h2>
            <a href="{{ route('meals.index') }}" class="text-pink-400 hover:text-pink-500 font-semibold transition-colors duration-200 flex items-center gap-2" aria-label="{{ __('Back to meal history') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                {{ __('Back to Meals') }}
            </a>
        </div>

        <!-- Meal Information and Photos -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg animate-fadeIn col-span-1">
                <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2">{{ __('Meal Information') }}</h3>
                <div class="space-y-4">
                    <p class="flex justify-between">
                        <strong class="text-gray-200">{{ __('Meal Type') }}:</strong>
                        <span class="text-gray-300">{{ ucfirst($meal->meal_type ?? __('N/A')) }}</span>
                    </p>
                    <p class="flex justify-between">
                        <strong class="text-gray-200">{{ __('Calories') }}:</strong>
                        <span class="text-gray-300">{{ isset($meal->analysis) && is_array($meal->analysis) && isset($meal->analysis['calories']) ? $meal->analysis['calories'] : ($meal->calories ?? __('N/A')) }} {{ __('kcal') }}</span>
                    </p>
                    <p class="flex justify-between">
                        <strong class="text-gray-200">{{ __('Feedback') }}:</strong>
                        <span class="text-gray-300">{{ $meal->feedback ?? __('N/A') }}</span>
                    </p>
                    <p class="flex justify-between">
                        <strong class="text-gray-200">{{ __('Status') }}:</strong>
                        <span class="text-gray-300 {{ $meal->status === 'pending' ? 'text-yellow-400' : ($meal->status === 'non_food_detected' ? 'text-red-400' : 'text-green-400') }}">{{ ucfirst(str_replace('_', ' ', $meal->status ?? __('N/A'))) }}</span>
                    </p>
                    <p class="flex justify-between">
                        <strong class="text-gray-200">{{ __('Health Condition') }}:</strong>
                        <span class="text-gray-300">{{ ucfirst($meal->health_condition ?? __('None')) }}</span>
                    </p>
                    <p class="flex justify-between">
                        <strong class="text-gray-200">{{ __('Uploaded') }}:</strong>
                        <span class="text-gray-300">{{ $meal->created_at ? $meal->created_at->setTimezone('Africa/Lagos')->format('M d, Y H:i') : __('N/A') }}</span>
                    </p>
                    @if ($profile = $meal->user->profile)
                        <p class="flex justify-between">
                            <strong class="text-gray-200">{{ __('User Goal') }}:</strong>
                            <span class="text-gray-300">{{ ucfirst($profile->goal) ?? __('N/A') }}</span>
                        </p>
                        <p class="flex justify-between">
                            <strong class="text-gray-200">{{ __('Daily Calories Goal') }}:</strong>
                            <span class="text-gray-300">{{ $profile->daily_calories ?? __('N/A') }} {{ __('kcal') }}</span>
                        </p>
                        <p class="flex justify-between">
                            <strong class="text-gray-200">{{ __('Health Conditions') }}:</strong>
                            <span class="text-gray-300">{{ is_array($profile->conditions) ? implode(', ', array_map('ucfirst', $profile->conditions)) : ($profile->conditions ?? __('None')) }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg animate-fadeIn lg:col-span-2">
                <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2">{{ __('Meal Photos') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-200 font-medium mb-2">{{ __('Meal Photo') }}</p>
                        <div class="relative group">
                            @if ($meal->photo_url && $meal->photo_exists)
                                <a href="{{ $meal->photo_url }}" target="_blank" aria-label="{{ __('Enlarge meal photo') }}">
                                    <img src="{{ $meal->photo_url }}" alt="{{ __('Meal photo') }}" class="w-full max-w-md object-cover rounded-xl shadow-md transition-transform duration-300 group-hover:scale-105" loading="lazy" onerror="this.src='{{ asset('images/nutrisnap-logo.png') }}'; console.error('Failed to load meal image: {{ $meal->photo_url }} for meal ID {{ $meal->id }}')">
                                    <div class="absolute inset-0 bg-gray-900/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ __('Click to enlarge') }}</span>
                                    </div>
                                </a>
                            @else
                                <img src="{{ asset('images/nutrisnap-logo.png') }}" alt="{{ __('Default meal photo') }}" class="w-full max-w-md object-cover rounded-xl shadow-md" loading="lazy">
                                @if ($meal->photo_url)
                                    <p class="text-red-400 text-sm mt-2">{{ __('Image not found for meal ID') }} {{ $meal->id }}: {{ $meal->photo_url }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if ($meal->leftover_photo_url && $meal->leftover_photo_exists)
                        <div>
                            <p class="text-gray-200 font-medium mb-2">{{ __('Leftover Photo') }}</p>
                            <div class="relative group">
                                <a href="{{ $meal->leftover_photo_url }}" target="_blank" aria-label="{{ __('Enlarge leftover photo') }}">
                                    <img src="{{ $meal->leftover_photo_url }}" alt="{{ __('Leftover photo') }}" class="w-full max-w-md object-cover rounded-xl shadow-md transition-transform duration-300 group-hover:scale-105" loading="lazy" onerror="this.src='{{ asset('images/nutrisnap-logo.png') }}'; console.error('Failed to load leftover image: {{ $meal->leftover_photo_url }} for meal ID {{ $meal->id }}')">
                                    <div class="absolute inset-0 bg-gray-900/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ __('Click to enlarge') }}</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @elseif ($meal->leftover_photo_url)
                        <div>
                            <p class="text-gray-200 font-medium mb-2">{{ __('Leftover Photo') }}</p>
                            <img src="{{ asset('images/nutrisnap-logo.png') }}" alt="{{ __('Default leftover photo') }}" class="w-full max-w-md object-cover rounded-xl shadow-md" loading="lazy">
                            <p class="text-red-400 text-sm mt-2">{{ __('Leftover image not found for meal ID') }} {{ $meal->id }}: {{ $meal->leftover_photo_url }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Analysis Details -->
        @if (isset($meal->analysis) && is_array($meal->analysis) && !empty($meal->analysis) || isset($meal->leftover_analysis) && is_array($meal->leftover_analysis) && !empty($meal->leftover_analysis))
            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn">
                <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2 flex justify-between items-center">
                    {{ __('Analysis Details') }}
                    <button id="toggleAnalysis" class="text-pink-400 hover:text-pink-500 font-medium transition-colors duration-200" aria-expanded="true" aria-controls="analysisContent">{{ __('Hide Details') }}</button>
                </h3>
                <div id="analysisContent" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if (isset($meal->analysis) && is_array($meal->analysis) && !empty($meal->analysis))
                        <div>
                            <h4 class="text-xl font-medium text-gray-200 mb-4">{{ __('Meal Analysis') }}</h4>
                            @if (isset($meal->analysis['error']))
                                <p class="text-red-400">{{ __('Error') }}: {{ $meal->analysis['error'] }}</p>
                            @else
                                <p class="flex justify-between">
                                    <strong class="text-gray-200">{{ __('Food Identified') }}:</strong>
                                    <span class="text-gray-300">
                                        @if (!empty($meal->analysis['food_items']) && is_array($meal->analysis['food_items']))
                                            {{ collect($meal->analysis['food_items'])->map(function ($prob, $food) {
                                                return "$food (" . number_format($prob * 100, 1) . "%)";
                                            })->implode(', ') ?: __('N/A') }}
                                        @else
                                            {{ $meal->analysis['food'] ?? __('N/A') }}
                                        @endif
                                    </span>
                                </p>
                                <p class="flex justify-between">
                                    <strong class="text-gray-200">{{ __('Calories') }}:</strong>
                                    <span class="text-gray-300">{{ $meal->analysis['calories'] ?? __('N/A') }} {{ __('kcal') }}</span>
                                </p>
                                @if (!empty($meal->analysis['macronutrients']) && is_array($meal->analysis['macronutrients']))
                                    <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Macronutrients') }}:</p>
                                    <div class="relative w-32 h-32 mx-auto">
                                        <canvas id="macroChart" role="img" aria-label="{{ __('Macronutrient distribution chart') }}"></canvas>
                                    </div>
                                    <ul class="list-disc pl-5 text-gray-300 space-y-1 mt-2">
                                        @foreach ($meal->analysis['macronutrients'] as $nutrient => $data)
                                            <li>{{ __(ucfirst($nutrient)) }}: {{ is_array($data) && isset($data['value']) ? $data['value'] : __('N/A') }}{{ is_array($data) && isset($data['unit']) ? $data['unit'] : '' }} ({{ is_array($data) && isset($data['percentage']) ? $data['percentage'] : __('N/A') }}%)</li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if (!empty($meal->analysis['micronutrients']) && is_array($meal->analysis['micronutrients']))
                                    <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Micronutrients') }}:</p>
                                    <ul class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach ($meal->analysis['micronutrients'] as $nutrient => $data)
                                            <li>{{ __(ucfirst(str_replace('_', ' ', $nutrient))) }}: {{ is_array($data) && isset($data['amount']) ? $data['amount'] : __('N/A') }}{{ is_array($data) && isset($data['unit']) ? $data['unit'] : '' }} ({{ is_array($data) && isset($data['percentage']) ? $data['percentage'] : __('N/A') }}% {{ __('DV') }})</li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if (!empty($meal->analysis['suitability']) && is_array($meal->analysis['suitability']))
                                    <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Dietary Suitability') }}:</p>
                                    <ul class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach ($meal->analysis['suitability'] as $key => $value)
                                            @if ($value)
                                                <li>{{ __(ucfirst(str_replace('is_', '', str_replace('_', ' ', $key)))) }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                                @if (!empty($meal->analysis['recommendations']) && is_array($meal->analysis['recommendations']))
                                    <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Recommendations') }}:</p>
                                    <ul class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach ($meal->analysis['recommendations'] as $rec)
                                            <li>{{ $rec }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if (!empty($meal->analysis['personalized_feedback']))
                                    <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Personalized Feedback') }}:</p>
                                    <p class="text-gray-300">{{ $meal->analysis['personalized_feedback'] }}</p>
                                @endif
                            @endif
                        </div>
                    @endif
                    @if (isset($meal->leftover_analysis) && is_array($meal->leftover_analysis) && !empty($meal->leftover_analysis))
                        <div>
                            <h4 class="text-xl font-medium text-gray-200 mb-4">{{ __('Leftover Analysis') }}</h4>
                            <p class="flex justify-between">
                                <strong class="text-gray-200">{{ __('Calories Consumed') }}:</strong>
                                <span class="text-gray-300">{{ $meal->leftover_analysis['calories'] ?? __('N/A') }} {{ __('kcal') }}</span>
                            </p>
                            @if (!empty($meal->leftover_analysis['macronutrients']) && is_array($meal->leftover_analysis['macronutrients']))
                                <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Macronutrients') }}:</p>
                                <div class="relative w-32 h-32 mx-auto">
                                    <canvas id="leftoverMacroChart" role="img" aria-label="{{ __('Leftover macronutrient distribution chart') }}"></canvas>
                                </div>
                                <ul class="list-disc pl-5 text-gray-300 space-y-1 mt-2">
                                    @foreach ($meal->leftover_analysis['macronutrients'] as $nutrient => $data)
                                        <li>{{ __(ucfirst($nutrient)) }}: {{ is_array($data) && isset($data['value']) ? $data['value'] : __('N/A') }}{{ is_array($data) && isset($data['unit']) ? $data['unit'] : '' }} ({{ is_array($data) && isset($data['percentage']) ? $data['percentage'] : __('N/A') }}%)</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if (!empty($meal->leftover_analysis['micronutrients']) && is_array($meal->leftover_analysis['micronutrients']))
                                <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Micronutrients') }}:</p>
                                <ul class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach ($meal->leftover_analysis['micronutrients'] as $nutrient => $data)
                                        <li>{{ __(ucfirst(str_replace('_', ' ', $nutrient))) }}: {{ is_array($data) && isset($data['amount']) ? $data['amount'] : __('N/A') }}{{ is_array($data) && isset($data['unit']) ? $data['unit'] : '' }} ({{ is_array($data) && isset($data['percentage']) ? $data['percentage'] : __('N/A') }}% {{ __('DV') }})</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if (!empty($meal->leftover_analysis['suitability']) && is_array($meal->leftover_analysis['suitability']))
                                <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Dietary Suitability') }}:</p>
                                <ul class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach ($meal->leftover_analysis['suitability'] as $key => $value)
                                        @if ($value)
                                            <li>{{ __(ucfirst(str_replace('is_', '', str_replace('_', ' ', $key)))) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                            @if (!empty($meal->leftover_analysis['recommendations']) && is_array($meal->leftover_analysis['recommendations']))
                                <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Recommendations') }}:</p>
                                <ul class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach ($meal->leftover_analysis['recommendations'] as $rec)
                                        <li>{{ $rec }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if (!empty($meal->leftover_analysis['personalized_feedback']))
                                <p class="text-gray-200 font-medium mt-4 mb-2">{{ __('Personalized Feedback') }}:</p>
                                <p class="text-gray-300">{{ $meal->leftover_analysis['personalized_feedback'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>
                @if ($profile = $meal->user->profile)
                    <div class="mt-6">
                        <h4 class="text-xl font-medium text-gray-200 mb-4">{{ __('Personalized Advice Based on Profile') }}</h4>
                        <div class="text-gray-300 space-y-2">
                            @if ($profile->goal && $profile->daily_calories)
                                <p>
                                    <strong>{{ __('Goal Alignment') }}:</strong>
                                    {{ __('Your goal is to') }} {{ __($profile->goal) }} {{ __('with a target of') }} {{ $profile->daily_calories }} {{ __('kcal/day') }}.
                                    @if (isset($meal->analysis) && is_array($meal->analysis) && isset($meal->analysis['calories']))
                                        @if ($meal->analysis['calories'] > $profile->daily_calories)
                                            {{ __('This meal\'s') }} {{ $meal->analysis['calories'] }} {{ __('kcal exceeds your daily target by') }} {{ $meal->analysis['calories'] - $profile->daily_calories }} {{ __('kcal. Consider smaller portions or lower-calorie alternatives.') }}
                                        @elseif ($meal->analysis['calories'] < $profile->daily_calories)
                                            {{ __('This meal provides') }} {{ $meal->analysis['calories'] }} {{ __('kcal, which is') }} {{ $profile->daily_calories - $meal->analysis['calories'] }} {{ __('kcal below your target. Include nutrient-dense foods to meet your goal.') }}
                                        @else
                                            {{ __('This meal\'s') }} {{ $meal->analysis['calories'] }} {{ __('kcal aligns with your daily target. Well done!') }}
                                        @endif
                                    @endif
                                </p>
                            @endif
                            @if ($profile->conditions && is_array($profile->conditions) && !empty($profile->conditions))
                                <p>
                                    <strong>{{ __('Health Considerations') }}:</strong>
                                    {{ __('Given your health conditions') }} ({{ implode(', ', array_map(function($condition) { return __(ucfirst($condition)); }, $profile->conditions)) }}), {{ __('ensure meals are') }}:
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($profile->conditions as $condition)
                                            @if ($condition === 'diabetes')
                                                <li>{{ __('Low in added sugars and high-glycemic foods to manage blood sugar.') }}</li>
                                            @elseif ($condition === 'hypertension')
                                                <li>{{ __('Low in sodium and rich in potassium to support blood pressure.') }}</li>
                                            @elseif ($condition === 'heart_disease')
                                                <li>{{ __('Low in saturated fats and high in omega-3s for heart health.') }}</li>
                                            @elseif ($condition === 'celiac')
                                                <li>{{ __('Gluten-free to avoid digestive issues.') }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </p>
                            @endif
                            @if ($profile->target_weight && $profile->weight)
                                <p>
                                    <strong>{{ __('Weight Goal Progress') }}:</strong>
                                    {{ __('Current weight') }}: {{ $profile->weight }} {{ __('kg') }}, {{ __('target') }}: {{ $profile->target_weight }} {{ __('kg') }}.
                                    @if ($profile->goal_days)
                                        {{ __('To achieve this in') }} {{ $profile->goal_days }} {{ __('days, aim for a daily calorie') }} {{ $profile->goal === 'lose_weight' ? __('deficit') : __('surplus') }} {{ __('of ~') }}{{ abs(($profile->weight - $profile->target_weight) * 7700 / $profile->goal_days) }} {{ __('kcal.') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Additional Personalized Dietary Advice -->
                <div class="mt-6">
                    <h4 class="text-xl font-medium text-gray-200 mb-4">{{ __('Additional Dietary Recommendations') }}</h4>
                    <div class="text-gray-300 space-y-2">
                        <p>
                            <strong>{{ __('Food Suggestions') }}:</strong>
                            {{ __('Based on your profile and meal analysis, consider') }}:
                            <ul class="list-disc pl-5 space-y-1">
                                @if ($profile && in_array('diabetes', $profile->conditions ?? []))
                                    <li>{{ __('Quinoa, leafy greens, berries (low-glycemic)') }}</li>
                                @endif
                                @if ($profile && in_array('hypertension', $profile->conditions ?? []))
                                    <li>{{ __('Bananas, spinach, unsalted nuts (potassium-rich)') }}</li>
                                @endif
                                @if ($profile && in_array('heart_disease', $profile->conditions ?? []))
                                    <li>{{ __('Salmon, walnuts, olive oil (omega-3 rich)') }}</li>
                                @endif
                                @if ($profile && in_array('celiac', $profile->conditions ?? []))
                                    <li>{{ __('Gluten-free grains: rice, buckwheat, certified oats') }}</li>
                                @endif
                                @if (!$profile || empty($profile->conditions))
                                    <li>{{ __('Lean proteins (chicken, tofu), whole grains (quinoa), vegetables (broccoli)') }}</li>
                                @endif
                            </ul>
                        </p>
                        <p>
                            <strong>{{ __('Dietary Tips') }}:</strong>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>{{ __('Balance macronutrients (~50% carbs, 20% protein, 30% fat)') }}</li>
                                <li>{{ __('Drink at least 8 glasses of water daily') }}</li>
                                <li>{{ __('Consult a dietitian for personalized plans') }}</li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Weekly Diet Plan -->
        @if ($profile = $meal->user->profile)
            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn">
                <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2">{{ __('Personalized Weekly Diet Plan') }}</h3>
                <p class="text-gray-300 mb-4">{{ __('Tailored to your') }} {{ __($profile->goal ?? 'health') }} {{ __('goal and') }} {{ $profile->daily_calories ?? __('balanced') }} {{ __('kcal/day target. Consult a dietitian for adjustments.') }}</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-300">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="p-4 text-sm font-semibold text-gray-200">{{ __('Day') }}</th>
                                <th class="p-4 text-sm font-semibold text-gray-200">{{ __('Meal') }}</th>
                                <th class="p-4 text-sm font-semibold text-gray-200">{{ __('Time') }}</th>
                                <th class="p-4 text-sm font-semibold text-gray-200">{{ __('Food') }}</th>
                                <th class="p-4 text-sm font-semibold text-gray-200">{{ __('Portion') }}</th>
                                <th class="p-4 text-sm font-semibold text-gray-200">{{ __('Calories') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $daily_calories = $profile->daily_calories ?? 2000;
                                $meals = [
                                    'Monday' => [
                                        ['Breakfast', '07:00', __('Greek yogurt with berries and chia seeds'), '200g yogurt, 100g berries, 1 tbsp chia', 250],
                                        ['Snack', '10:00', __('Almonds'), '30g', 180],
                                        ['Lunch', '13:00', __('Grilled chicken breast, quinoa, steamed broccoli'), '150g chicken, 100g quinoa, 200g broccoli', 450],
                                        ['Snack', '16:00', __('Apple with peanut butter'), '1 medium apple, 2 tbsp peanut butter', 200],
                                        ['Dinner', '19:00', __('Baked salmon, sweet potato, mixed greens'), '150g salmon, 150g sweet potato, 100g greens', 400],
                                    ],
                                    'Tuesday' => [
                                        ['Breakfast', '07:00', __('Oatmeal with banana and walnuts'), '50g oats, 1 banana, 20g walnuts', 300],
                                        ['Snack', '10:00', __('Carrot sticks with hummus'), '100g carrots, 2 tbsp hummus', 150],
                                        ['Lunch', '13:00', __('Turkey wrap with avocado, spinach'), '100g turkey, 1 wrap, 50g avocado, 50g spinach', 400],
                                        ['Snack', '16:00', __('Greek yogurt with honey'), '150g yogurt, 1 tsp honey', 150],
                                        ['Dinner', '19:00', __('Grilled tofu, brown rice, asparagus'), '150g tofu, 100g rice, 150g asparagus', 400],
                                    ],
                                    'Wednesday' => [
                                        ['Breakfast', '07:00', __('Scrambled eggs with spinach and tomatoes'), '2 eggs, 50g spinach, 100g tomatoes', 200],
                                        ['Snack', '10:00', __('Mixed berries'), '150g', 80],
                                        ['Lunch', '13:00', __('Lentil soup, mixed greens salad'), '200ml soup, 100g salad', 350],
                                        ['Snack', '16:00', __('Rice cakes with almond butter'), '2 rice cakes, 1 tbsp almond butter', 150],
                                        ['Dinner', '19:00', __('Grilled shrimp, zucchini noodles, olive oil'), '150g shrimp, 200g zucchini, 1 tbsp oil', 350],
                                    ],
                                    'Thursday' => [
                                        ['Breakfast', '07:00', __('Smoothie with spinach, banana, protein powder'), '100g spinach, 1 banana, 25g protein', 250],
                                        ['Snack', '10:00', __('Celery with cream cheese'), '2 stalks, 2 tbsp cream cheese', 100],
                                        ['Lunch', '13:00', __('Grilled chicken salad with avocado, cucumber'), '150g chicken, 50g avocado, 100g cucumber', 400],
                                        ['Snack', '16:00', __('Hard-boiled egg'), '1 egg', 70],
                                        ['Dinner', '19:00', __('Baked cod, roasted Brussels sprouts'), '150g cod, 200g Brussels sprouts', 350],
                                    ],
                                    'Friday' => [
                                        ['Breakfast', '07:00', __('Whole-grain toast with avocado'), '1 slice, 50g avocado', 200],
                                        ['Snack', '10:00', __('Mixed nuts'), '30g', 180],
                                        ['Lunch', '13:00', __('Quinoa bowl with black beans, corn, salsa'), '100g quinoa, 100g beans, 50g corn', 400],
                                        ['Snack', '16:00', __('Cottage cheese with pineapple'), '100g cottage cheese, 100g pineapple', 150],
                                        ['Dinner', '19:00', __('Grilled chicken, roasted root vegetables'), '150g chicken, 200g vegetables', 400],
                                    ],
                                    'Saturday' => [
                                        ['Breakfast', '07:00', __('Chia pudding with mango'), '200ml chia pudding, 100g mango', 250],
                                        ['Snack', '10:00', __('Edamame'), '100g', 120],
                                        ['Lunch', '13:00', __('Tuna salad with cucumber, red pepper'), '100g tuna, 100g cucumber, 50g pepper', 350],
                                        ['Snack', '16:00', __('Banana'), '1 medium banana', 90],
                                        ['Dinner', '19:00', __('Grilled pork tenderloin, mashed cauliflower'), '150g pork, 200g cauliflower', 400],
                                    ],
                                    'Sunday' => [
                                        ['Breakfast', '07:00', __('Avocado toast with poached egg'), '1 slice, 50g avocado, 1 egg', 250],
                                        ['Snack', '10:00', __('Greek yogurt with granola'), '150g yogurt, 20g granola', 200],
                                        ['Lunch', '13:00', __('Grilled salmon, quinoa, kale salad'), '150g salmon, 100g quinoa, 100g kale', 450],
                                        ['Snack', '16:00', __('Orange slices'), '1 medium orange', 60],
                                        ['Dinner', '19:00', __('Vegetable stir-fry with tofu'), '150g tofu, 200g mixed vegetables', 350],
                                    ],
                                ];
                                foreach ($meals as $day => &$day_meals) {
                                    foreach ($day_meals as &$meal) {
                                        if ($profile && in_array('celiac', $profile->conditions ?? [])) {
                                            $meal[2] .= ' (' . __('gluten-free') . ')';
                                        }
                                        if ($profile && in_array('hypertension', $profile->conditions ?? [])) {
                                            $meal[2] .= ' (' . __('low-sodium') . ')';
                                        }
                                        if ($profile && in_array('diabetes', $profile->conditions ?? [])) {
                                            $meal[2] .= ' (' . __('low-glycemic') . ')';
                                        }
                                        if ($profile && in_array('heart_disease', $profile->conditions ?? [])) {
                                            $meal[2] .= ' (' . __('heart-healthy') . ')';
                                        }
                                        // Scale calories based on user's daily goal
                                        $meal[4] = round($meal[4] * ($daily_calories / 2000));
                                    }
                                }
                            @endphp
                            @foreach ($meals as $day => $day_meals)
                                @foreach ($day_meals as $index => $meal)
                                    <tr class="border-b border-gray-700 hover:bg-gray-600/50 transition-colors duration-200">
                                        @if ($index === 0)
                                            <td class="p-4" rowspan="{{ count($day_meals) }}">{{ __($day) }}</td>
                                        @endif
                                        <td class="p-4">{{ __($meal[0]) }}</td>
                                        <td class="p-4">{{ $meal[1] }}</td>
                                        <td class="p-4 relative group">
                                            {{ $meal[2] }}
                                            @if (strpos($meal[2], '(') !== false)
                                                <span class="tooltip hidden group-hover:block">{{ str_replace(['(', ')'], '', strstr($meal[2], '(')) }} {{ __('for your health needs') }}.</span>
                                            @endif
                                        </td>
                                        <td class="p-4">{{ $meal[3] }}</td>
                                        <td class="p-4">{{ $meal[4] }} {{ __('kcal') }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-6">
                    <form id="downloadTimetable" action="{{ route('diet.download') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-pink-600 to-purple-600 text-white px-8 py-3 rounded-lg hover:from-pink-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed" aria-label="{{ __('Download diet plan as PDF') }}">{{ __('Download Timetable as PDF') }}</button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Social Sharing -->
        @if (isset($meal->share_link) && is_array($meal->share_link) && !empty($meal->share_link) || isset($meal->share_proof) && is_array($meal->share_proof) && !empty($meal->share_proof))
            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn">
                <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2">{{ __('Social Sharing') }}</h3>
                @if (isset($meal->share_link) && is_array($meal->share_link) && !empty($meal->share_link))
                    <p class="text-gray-200 font-medium mb-4">{{ __('Share Links') }}:</p>
                    <div class="flex flex-wrap gap-4">
                        @foreach ($meal->share_link as $platform => $url)
                            @if (is_string($url) && !empty($url))
                                <div class="relative group">
                                    <a href="{{ $url }}" target="_blank" class="px-5 py-2.5 rounded-lg text-white font-medium {{ $platform === 'facebook' ? 'bg-blue-600 hover:bg-blue-700' : ($platform === 'instagram' ? 'bg-pink-600 hover:bg-pink-700' : ($platform === 'youtube' ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-600 hover:bg-gray-700')) }} transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-1" aria-label="{{ __('Share on') }} {{ __(ucfirst($platform)) }}">{{ __(ucfirst($platform)) }}</a>
                                    <button class="copy-link absolute -top-2 -right-2 w-6 h-6 bg-gray-700 rounded-full text-gray-300 hover:text-white transition-colors duration-200" data-url="{{ $url }}" aria-label="{{ __('Copy') }} {{ __(ucfirst($platform)) }} {{ __('link') }}">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                @php
                                    \Log::warning('Invalid share_link entry for meal ID ' . $meal->id, [
                                        'platform' => $platform,
                                        'url' => $url,
                                        'type' => gettype($url),
                                    ]);
                                @endphp
                            @endif
                        @endforeach
                    </div>
                @endif
                @if (isset($meal->share_proof) && is_array($meal->share_proof) && !empty($meal->share_proof))
                    <p class="text-gray-200 font-medium mt-6 mb-4">{{ __('Share Proof') }}:</p>
                    <ul class="list-disc pl-5 text-gray-300 space-y-2">
                        @foreach ($meal->share_proof as $platform => $url)
                            @if (is_string($url) && !empty($url))
                                <li>
                                    {{ __(ucfirst($platform)) }}:
                                    <a href="{{ $url }}" target="_blank" class="text-pink-400 hover:text-pink-500 underline transition-colors duration-200" aria-label="{{ __(ucfirst($platform)) }} {{ __('share proof') }}">{{ $url }}</a>
                                </li>
                            @else
                                @php
                                    \Log::warning('Invalid share_proof entry for meal ID ' . $meal->id, [
                                        'platform' => $platform,
                                        'url' => $url,
                                        'type' => gettype($url),
                                    ]);
                                @endphp
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <!-- Correction Request Form -->
        <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn">
            <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2">{{ __('Request Correction') }}</h3>
            <form id="correctionForm" action="{{ route('meals.requestCorrection', $meal) }}" method="POST" class="space-y-6" novalidate>
                @csrf
                <div>
                    <label for="corrections" class="block text-sm font-medium text-gray-200">{{ __('Corrections') }} <span class="text-red-400" aria-hidden="true">*</span></label>
                    <textarea name="corrections" id="corrections" rows="5" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-4 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md resize-y" required placeholder="{{ __('Describe the corrections needed (e.g., incorrect food identification, calorie mismatch)') }}" aria-describedby="corrections-error"></textarea>
                    @error('corrections')
                        <p id="corrections-error" class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @else
                        <p id="corrections-error" class="text-red-400 text-sm mt-2 hidden"></p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="calories" class="block text-sm font-medium text-gray-200">{{ __('Corrected Calories (kcal)') }}</label>
                        <input type="number" name="calories" id="calories" min="0" step="1" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" placeholder="{{ __('Enter corrected calories (optional)') }}" aria-describedby="calories-error">
                        @error('calories')
                            <p id="calories-error" class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @else
                            <p id="calories-error" class="text-red-400 text-sm mt-2 hidden"></p>
                        @enderror
                    </div>
                    <div>
                        <label for="food" class="block text-sm font-medium text-gray-200">{{ __('Corrected Food Description') }}</label>
                        <input type="text" name="food" id="food" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" placeholder="{{ __('Enter corrected food description (optional)') }}" aria-describedby="food-error">
                        @error('food')
                            <p id="food-error" class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @else
                            <p id="food-error" class="text-red-400 text-sm mt-2 hidden"></p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="submit" class="bg-gradient-to-r from-pink-600 to-purple-600 text-white px-8 py-3 rounded-lg hover:from-pink-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed" aria-label="{{ __('Submit correction request') }}">{{ __('Submit Correction') }}</button>
                    <form id="deleteForm" action="{{ route('meals.destroy', $meal) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed" aria-label="{{ __('Delete meal') }}">{{ __('Delete Meal') }}</button>
                    </form>
                </div>
            </form>
        </div>

        <!-- Debug Form (Hidden by Default) -->
        <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn" id="debugSection" style="display: none;">
            <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2 flex justify-between items-center">
                {{ __('Debug: Native Correction Form') }}
                <button id="toggleDebug" class="text-pink-400 hover:text-pink-500 font-medium transition-colors duration-200" aria-expanded="false" aria-controls="debugForm">{{ __('Show Debug Form') }}</button>
            </h3>
            <div id="debugForm" class="space-y-6 hidden">
                <p class="text-gray-300 mb-4">{{ __('Use this form to test native POST submission without JavaScript.') }}</p>
                <form action="{{ route('meals.requestCorrection', $meal) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="debug_corrections" class="block text-sm font-medium text-gray-200">{{ __('Corrections') }} <span class="text-red-400" aria-hidden="true">*</span></label>
                        <textarea name="corrections" id="debug_corrections" rows="5" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-4 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md resize-y" required placeholder="{{ __('Describe the corrections needed') }}"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="debug_calories" class="block text-sm font-medium text-gray-200">{{ __('Corrected Calories (kcal)') }}</label>
                            <input type="number" name="calories" id="debug_calories" min="0" step="1" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" placeholder="{{ __('Enter corrected calories (optional)') }}">
                        </div>
                        <div>
                            <label for="debug_food" class="block text-sm font-medium text-gray-200">{{ __('Corrected Food Description') }}</label>
                            <input type="text" name="food" id="debug_food" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" placeholder="{{ __('Enter corrected food description (optional)') }}">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-pink-600 to-purple-600 text-white px-8 py-3 rounded-lg hover:from-pink-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1" aria-label="{{ __('Submit debug correction') }}">{{ __('Submit Debug Correction') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SweetAlert2 and Chart.js CDNs -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.1/dist/sweetalert2.all.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
            // Pass translations to JavaScript
            const translations = {
                protein: '{{ __('Protein') }}',
                carbohydrates: '{{ __('Carbohydrates') }}',
                fat: '{{ __('Fat') }}',
                success: '{{ __('Success') }}',
                error: '{{ __('Error') }}',
                link_copied: '{{ __('Share link copied to clipboard!') }}',
                copy_failed: '{{ __('Failed to copy share link.') }}',
                show_details: '{{ __('Show Details') }}',
                hide_details: '{{ __('Hide Details') }}',
                show_debug_form: '{{ __('Show Debug Form') }}',
                hide_debug_form: '{{ __('Hide Debug Form') }}',
                confirm_delete: '{{ __('Are you sure?') }}',
                delete_message: '{{ __('This meal will be deleted permanently.') }}',
                yes_delete: '{{ __('Yes, delete it!') }}',
                cancel: '{{ __('Cancel') }}',
                validation_error: '{{ __('Validation Error') }}',
                validation_message: '{{ __('Please correct the errors in the form.') }}',
                corrections_error: '{{ __('Please provide a detailed description (at least 10 characters).') }}',
                calories_error: '{{ __('Calories must be a non-negative number.') }}'
            };

            document.addEventListener('DOMContentLoaded', function () {
                const correctionForm = document.getElementById('correctionForm');
                const deleteForm = document.getElementById('deleteForm');
                const downloadForm = document.getElementById('downloadTimetable');
                const toggleAnalysis = document.getElementById('toggleAnalysis');
                const analysisContent = document.getElementById('analysisContent');
                const toggleDebug = document.getElementById('toggleDebug');
                const debugForm = document.getElementById('debugForm');
                const loadingOverlay = document.getElementById('loadingOverlay');
                let macroChart = null;
                let leftoverMacroChart = null;
                let isSubmitting = false;

                console.log('DOMContentLoaded fired. Initializing meal show scripts...');

                // Utility: Debounce function
                function debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }

                // Utility: Show SweetAlert
                function showAlert(icon, title, text, callback = null) {
                    Swal.fire({
                        icon,
                        title,
                        text,
                        background: '#1f2937',
                        color: '#f3f4f6',
                        confirmButtonColor: '#ec4899',
                        confirmButtonText: '{{ __('OK') }}',
                        backdrop: 'rgba(0, 0, 0, 0.8)',
                    }).then(callback);
                }

                // Utility: Handle API Request
                async function handleApiRequest(form, method = 'POST', successRedirect = '{{ route('meals.index') }}') {
                    if (isSubmitting) {
                        console.log('Submission blocked: already submitting');
                        return;
                    }
                    isSubmitting = true;
                    loadingOverlay.classList.remove('hidden');

                    const formData = new FormData(form);
                    if (method === 'DELETE' && !formData.has('_method')) {
                        formData.append('_method', 'DELETE');
                    }

                    console.log(`${method} form submission`, {
                        form_id: form.id,
                        action: form.action,
                        method,
                        formData: Object.fromEntries(formData),
                    });

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                            },
                        });

                        console.log(`${method} response`, {
                            status: response.status,
                            statusText: response.statusText,
                            url: response.url,
                            headers: Object.fromEntries(response.headers.entries()),
                        });

                        let data;
                        try {
                            data = await response.json();
                            console.log('Parsed response data', data);
                        } catch (e) {
                            console.warn('Failed to parse JSON', { error: e.message });
                            data = { success: false, message: '{{ __('Invalid response format') }}' };
                        }

                        loadingOverlay.classList.add('hidden');
                        isSubmitting = false;

                        if (response.ok && data.success) {
                            showAlert('success', translations.success, data.message, () => {
                                console.log(`Redirecting to ${successRedirect}`);
                                if (data.download_url && method === 'POST' && form.id === 'downloadTimetable') {
                                    window.location.href = data.download_url;
                                } else {
                                    window.location.href = successRedirect;
                                }
                            });
                        } else {
                            console.warn(`${method} request failed`, {
                                status: response.status,
                                message: data.message,
                            });
                            showAlert('error', translations.error, data.message || `{{ __('Failed to process') }} ${method.toLowerCase()} {{ __('request') }}.`);
                        }
                    } catch (error) {
                        console.error(`${method} request error`, {
                            error: error.message,
                            stack: error.stack,
                        });
                        loadingOverlay.classList.add('hidden');
                        isSubmitting = false;
                        showAlert('error', translations.error, `{{ __('Failed to process') }} ${method.toLowerCase()} {{ __('request') }}: ${error.message}`);
                    }
                }

                // Initialize Macronutrient Chart
                function initializeMacroChart(canvasId, data) {
                    const ctx = document.getElementById(canvasId)?.getContext('2d');
                    if (!ctx) {
                        console.error(`Canvas element #${canvasId} not found`);
                        return null;
                    }

                    try {
                        const chartData = {
                            protein: typeof data?.protein === 'object' && data.protein.percentage ? data.protein.percentage : 0,
                            carbohydrates: typeof data?.carbs === 'object' && data.carbs.percentage ? data.carbs.percentage : 0,
                            fat: typeof data?.fat === 'object' && data.fat.percentage ? data.fat.percentage : 0
                        };

                        const chart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: [translations.protein, translations.carbohydrates, translations.fat],
                                datasets: [{
                                    data: [
                                        chartData.protein,
                                        chartData.carbohydrates,
                                        chartData.fat
                                    ],
                                    backgroundColor: ['#ec4899', '#8b5cf6', '#3b82f6'],
                                    borderColor: '#1f2937',
                                    borderWidth: 2,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            color: '#f3f4f6',
                                            font: { size: 12 },
                                        },
                                    },
                                    tooltip: {
                                        backgroundColor: '#1f2937',
                                        titleColor: '#f3f4f6',
                                        bodyColor: '#d1d5db',
                                        callbacks: {
                                            label: function(context) {
                                                return `${context.label}: ${context.raw}%`;
                                            }
                                        },
                                    },
                                },
                            },
                        });
                        console.log(`Macronutrient chart ${canvasId} initialized`, chartData);
                        return chart;
                    } catch (error) {
                        console.error(`Macronutrient chart ${canvasId} initialization failed:`, error);
                        return null;
                    }
                }

                // Initialize Charts
                @if (isset($meal->analysis) && is_array($meal->analysis) && !empty($meal->analysis['macronutrients']) && is_array($meal->analysis['macronutrients']))
                    macroChart = initializeMacroChart('macroChart', @json($meal->analysis['macronutrients']));
                @endif
                @if (isset($meal->leftover_analysis) && is_array($meal->leftover_analysis) && !empty($meal->leftover_analysis['macronutrients']) && is_array($meal->leftover_analysis['macronutrients']))
                    leftoverMacroChart = initializeMacroChart('leftoverMacroChart', @json($meal->leftover_analysis['macronutrients']));
                @endif

                // Toggle Analysis Details
                if (toggleAnalysis && analysisContent) {
                    toggleAnalysis.addEventListener('click', () => {
                        const isExpanded = toggleAnalysis.getAttribute('aria-expanded') === 'true';
                        analysisContent.classList.toggle('hidden', isExpanded);
                        toggleAnalysis.setAttribute('aria-expanded', !isExpanded);
                        toggleAnalysis.textContent = isExpanded ? translations.show_details : translations.hide_details;
                    });
                }

                // Toggle Debug Form
                if (toggleDebug && debugForm) {
                    toggleDebug.addEventListener('click', () => {
                        const isExpanded = toggleDebug.getAttribute('aria-expanded') === 'true';
                        debugForm.classList.toggle('hidden', isExpanded);
                        toggleDebug.setAttribute('aria-expanded', !isExpanded);
                        toggleDebug.textContent = isExpanded ? translations.show_debug_form : translations.hide_debug_form;
                    });
                    // Show debug section for admins or debug=true query param
                    if (window.location.search.includes('debug=true') || {{ auth()->user()->role ?? 'null' }} === 'admin') {
                        document.getElementById('debugSection').style.display = 'block';
                    }
                }

                // Form Validation
                function validateForm(form) {
                    const corrections = form.querySelector('#corrections');
                    const calories = form.querySelector('#calories');
                    let isValid = true;

                    if (corrections.value.trim().length < 10) {
                        document.getElementById('corrections-error').textContent = translations.corrections_error;
                        document.getElementById('corrections-error').classList.remove('hidden');
                        isValid = false;
                    } else {
                        document.getElementById('corrections-error').classList.add('hidden');
                    }

                    if (calories.value && (calories.value < 0 || isNaN(calories.value))) {
                        document.getElementById('calories-error').textContent = translations.calories_error;
                        document.getElementById('calories-error').classList.remove('hidden');
                        isValid = false;
                    } else {
                        document.getElementById('calories-error').classList.add('hidden');
                    }

                    return isValid;
                }

                // Correction Form Submission
                if (correctionForm) {
                    correctionForm.addEventListener('submit', debounce(async function (e) {
                        e.preventDefault();
                        if (!validateForm(correctionForm)) {
                            showAlert('error', translations.validation_error, translations.validation_message);
                            return;
                        }
                        await handleApiRequest(correctionForm);
                    }, 300));
                }

                // Delete Form Submission
                if (deleteForm) {
                    deleteForm.addEventListener('submit', debounce(async function (e) {
                        e.preventDefault();
                        const result = await Swal.fire({
                            title: translations.confirm_delete,
                            text: translations.delete_message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ec4899',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: translations.yes_delete,
                            cancelButtonText: translations.cancel,
                            background: '#1f2937',
                            color: '#f3f4f6',
                            backdrop: 'rgba(0, 0, 0, 0.8)',
                        });

                        if (result.isConfirmed) {
                            await handleApiRequest(deleteForm, 'DELETE');
                        }
                    }, 300));
                }

                // Download Timetable Submission
                if (downloadForm) {
                    downloadForm.addEventListener('submit', debounce(async function (e) {
                        e.preventDefault();
                        await handleApiRequest(downloadForm, 'POST', null);
                    }, 300));
                }

                // Copy Link Functionality
                document.querySelectorAll('.copy-link').forEach(button => {
                    button.addEventListener('click', async () => {
                        const url = button.getAttribute('data-url');
                        try {
                            await navigator.clipboard.writeText(url);
                            showAlert('success', translations.success, translations.link_copied);
                        } catch (error) {
                            console.error('Copy link error', error);
                            showAlert('error', translations.error, translations.copy_failed);
                        }
                    });
                });

                // Log analysis and leftover_analysis for debugging
                console.log('Meal analysis:', {{ isset($meal->analysis) && is_array($meal->analysis) ? json_encode($meal->analysis) : 'null' }});
                console.log('Meal leftover_analysis:', {{ isset($meal->leftover_analysis) && is_array($meal->leftover_analysis) ? json_encode($meal->leftover_analysis) : 'null' }});
                console.log('Meal share_link:', {{ isset($meal->share_link) && is_array($meal->share_link) ? json_encode($meal->share_link) : 'null' }});
                console.log('Meal share_proof:', {{ isset($meal->share_proof) && is_array($meal->share_proof) ? json_encode($meal->share_proof) : 'null' }});
            });
        </script>
        <style>
            .animate-slideUp {
                animation: slideUp 0.5s ease-out;
            }
            .animate-fadeIn {
                animation: fadeIn 0.5s ease-out;
            }
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            .tooltip {
                @apply absolute z-10 bg-gray-800 text-gray-200 text-xs rounded-lg p-2 w-48 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200;
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
            }
            @keyframes slideUp {
                from { transform: translateY(20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            @media (max-width: 640px) {
                table {
                    font-size: 0.75rem;
                }
                th, td {
                    padding: 0.5rem;
                }
                .tooltip {
                    width: 80%;
                    left: 10%;
                }
            }
        </style>
    </div>
@endsection