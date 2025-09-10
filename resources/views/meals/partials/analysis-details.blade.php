<div class="analysis-details hidden mt-1 bg-gray-900 p-2 rounded-md" data-meal-id="{{ $meal->id }}">
    <h4 class="text-sm font-semibold text-white mb-1">Analysis Details</h4>
    @if (!empty($meal->analysis) && is_array($meal->analysis))
        @if ($meal->analysis['is_non_food'] ?? false)
            <div class="bg-red-900/30 p-1 rounded-sm mb-1">
                <p class="text-red-400 text-xs">No food detected: {{ !empty($meal->analysis['non_food_items']) ? implode(', ', $meal->analysis['non_food_items']) : 'None' }}</p>
            </div>
            <div class="grid grid-cols-1 gap-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-200">Source:</span>
                    <span class="text-gray-300">{{ $meal->analysis['source'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-200">Cropped:</span>
                    <span class="text-gray-300">{{ isset($meal->analysis['was_cropped']) ? ($meal->analysis['was_cropped'] ? 'Yes' : 'No') : 'N/A' }}</span>
                </div>
                @if (!empty($meal->analysis['non_food_items']))
                    <div>
                        <p class="text-gray-200 mb-0.5">Non-Food:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($meal->analysis['non_food_items'] as $item)
                                <span class="bg-gray-700 text-gray-300 text-xs px-1 py-0.5 rounded">{{ $item ?: 'Unknown' }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 gap-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-200">Food:</span>
                    <span class="text-gray-300">{{ collect($meal->analysis['food_items'] ?? [])->map(fn($prob, $food) => "$food (" . number_format($prob * 100, 1) . "%)")->implode(', ') ?: 'None' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-200">Calories:</span>
                    <span class="text-gray-300">{{ $meal->analysis['calories'] ?? 'N/A' }} kcal</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-200">Source:</span>
                    <span class="text-gray-300">{{ $meal->analysis['source'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-200">Cropped:</span>
                    <span class="text-gray-300">{{ isset($meal->analysis['was_cropped']) ? ($meal->analysis['was_cropped'] ? 'Yes' : 'No') : 'N/A' }}</span>
                </div>
                @if (!empty($meal->analysis['nutrients']) || !empty($meal->analysis['macronutrients']) || !empty($meal->analysis['micronutrients']))
                    <div class="bg-gray-800 p-1 rounded-sm">
                        <p class="text-gray-200 mb-0.5">Nutrients:</p>
                        <div class="grid grid-cols-2 gap-1 text-xs">
                            @foreach ($meal->analysis['nutrients'] ?? [] as $key => $value)
                                <div class="flex justify-between">
                                    <span class="text-gray-300">{{ ucfirst($key) }}:</span>
                                    <span class="text-gray-300">{{ $value ?? 0 }}g</span>
                                </div>
                            @endforeach
                            @foreach ($meal->analysis['macronutrients'] ?? [] as $key => $value)
                                <div class="flex justify-between">
                                    <span class="text-gray-300">{{ ucfirst($key) }}:</span>
                                    <span class="text-gray-300">{{ $value['value'] ?? 0 }}{{ $value['unit'] ?? 'g' }} ({{ $value['percentage'] ?? 0 }}%)</span>
                                </div>
                            @endforeach
                            @foreach ($meal->analysis['micronutrients'] ?? [] as $key => $value)
                                <div class="flex justify-between">
                                    <span class="text-gray-300">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="text-gray-300">{{ is_array($value) ? ($value['value'] ?? 0) : $value }}{{ is_array($value) ? ($value['unit'] ?? 'mg') : 'mg' }} ({{ is_array($value) ? ($value['percentage'] ?? 0) : 0 }}% DV)</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if (!empty($meal->analysis['ingredients']))
                    <div>
                        <p class="text-gray-200 mb-0.5">Ingredients:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($meal->analysis['ingredients'] as $item)
                                <span class="bg-gray-700 text-gray-300 text-xs px-1 py-0.5 rounded">{{ $item['name'] ?? $item ?? 'Unknown' }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @else
        <p class="text-gray-400 text-xs">No analysis data.</p>
    @endif

    @if (!empty($meal->leftover_analysis) && is_array($meal->leftover_analysis))
        <div class="mt-1">
            <h4 class="text-sm font-semibold text-white mb-1">Leftover Analysis</h4>
            @if ($meal->leftover_analysis['is_non_food'] ?? false)
                <div class="bg-red-900/30 p-1 rounded-sm mb-1">
                    <p class="text-red-400 text-xs">No food in leftovers: {{ !empty($meal->leftover_analysis['non_food_items']) ? implode(', ', $meal->leftover_analysis['non_food_items']) : 'None' }}</p>
                </div>
                <div class="grid grid-cols-1 gap-1 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-200">Source:</span>
                        <span class="text-gray-300">{{ $meal->leftover_analysis['source'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-200">Cropped:</span>
                        <span class="text-gray-300">{{ isset($meal->leftover_analysis['was_cropped']) ? ($meal->leftover_analysis['was_cropped'] ? 'Yes' : 'No') : 'N/A' }}</span>
                    </div>
                    @if (!empty($meal->leftover_analysis['non_food_items']))
                        <div>
                            <p class="text-gray-200 mb-0.5">Non-Food:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($meal->leftover_analysis['non_food_items'] as $item)
                                    <span class="bg-gray-700 text-gray-300 text-xs px-1 py-0.5 rounded">{{ $item ?: 'Unknown' }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 gap-1 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-200">Calories:</span>
                        <span class="text-gray-300">{{ $meal->leftover_analysis['calories'] ?? 'N/A' }} kcal</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-200">Source:</span>
                        <span class="text-gray-300">{{ $meal->leftover_analysis['source'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-200">Cropped:</span>
                        <span class="text-gray-300">{{ isset($meal->leftover_analysis['was_cropped']) ? ($meal->leftover_analysis['was_cropped'] ? 'Yes' : 'No') : 'N/A' }}</span>
                    </div>
                    @if (!empty($meal->leftover_analysis['nutrients']) || !empty($meal->leftover_analysis['macronutrients']) || !empty($meal->leftover_analysis['micronutrients']))
                        <div class="bg-gray-800 p-1 rounded-sm">
                            <p class="text-gray-200 mb-0.5">Nutrients:</p>
                            <div class="grid grid-cols-2 gap-1 text-xs">
                                @foreach ($meal->leftover_analysis['nutrients'] ?? [] as $key => $value)
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">{{ ucfirst($key) }}:</span>
                                        <span class="text-gray-300">{{ $value ?? 0 }}g</span>
                                    </div>
                                @endforeach
                                @foreach ($meal->leftover_analysis['macronutrients'] ?? [] as $key => $value)
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">{{ ucfirst($key) }}:</span>
                                        <span class="text-gray-300">{{ $value['value'] ?? 0 }}{{ $value['unit'] ?? 'g' }} ({{ $value['percentage'] ?? 0 }}%)</span>
                                    </div>
                                @endforeach
                                @foreach ($meal->leftover_analysis['micronutrients'] ?? [] as $key => $value)
                                    <div class="flex justify-between">
                                        <span class="text-gray-300">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="text-gray-300">{{ is_array($value) ? ($value['value'] ?? 0) : $value }}{{ is_array($value) ? ($value['unit'] ?? 'mg') : 'mg' }} ({{ is_array($value) ? ($value['percentage'] ?? 0) : 0 }}% DV)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (!empty($meal->leftover_analysis['ingredients']))
                        <div>
                            <p class="text-gray-200 mb-0.5">Ingredients:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($meal->leftover_analysis['ingredients'] as $item)
                                    <span class="bg-gray-700 text-gray-300 text-xs px-1 py-0.5 rounded">{{ $item['name'] ?? $item ?? 'Unknown' }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @else
        <p class="text-gray-400 text-xs">No leftover analysis data.</p>
    @endif
</div>