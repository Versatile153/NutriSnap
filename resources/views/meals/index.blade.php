@extends('layouts.app1')

@section('content')
    <div class="max-w-7xl mx-auto bg-gray-900/95 backdrop-blur-md p-6 sm:p-8 lg:p-12 rounded-3xl shadow-2xl animate-slideUp mt-10">
        <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-400 via-purple-400 to-indigo-400 tracking-tight">{{ __('NutriSnap: Your Meal Analyzer') }}</h2>
            <a href="{{ route('profile.edit') }}" class="text-pink-400 hover:text-pink-500 font-semibold transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A8.001 8.001 0 0112 4a8 8 0 016.879 13.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ __('Profile') }}
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="bg-green-900/80 p-4 rounded-lg mb-4">
                <p class="text-green-400 font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-900/80 p-4 rounded-lg mb-4">
                <p class="text-red-400 font-medium">{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-900/80 p-4 rounded-lg mb-4">
                <ul class="list-disc pl-5 text-red-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Upload Form -->
        <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn">
            <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2">{{ __('Upload Your Meal') }}</h3>
            <form id="uploadForm" action="{{ route('meals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-200">{{ __('Food Photo') }} <span class="text-red-400">*</span></label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 file:bg-gradient-to-r file:from-pink-600 file:to-purple-600 file:text-white file:border-none file:rounded-lg file:px-4 file:py-2 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" required>
                        <div id="photoPreview" class="mt-2 hidden">
                            <img src="" alt="{{ __('Photo Preview') }}" class="w-32 h-32 object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105 cursor-pointer">
                        </div>
                        @error('photo')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="meal_type" class="block text-sm font-medium text-gray-200">{{ __('Meal Type') }} <span class="text-red-400">*</span></label>
                        <select name="meal_type" id="meal_type" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" required>
                            <option value="" disabled selected>{{ __('Select Meal Type') }}</option>
                            <option value="breakfast">{{ __('Breakfast') }}</option>
                            <option value="lunch">{{ __('Lunch') }}</option>
                            <option value="dinner">{{ __('Dinner') }}</option>
                            <option value="snack">{{ __('Snack') }}</option>
                        </select>
                        @error('meal_type')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="portion_size" class="block text-sm font-medium text-gray-200">{{ __('Portion Size (grams)') }}</label>
                        <input type="number" name="portion_size" id="portion_size" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" placeholder="{{ __('Enter portion size') }}">
                        @error('portion_size')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="health_condition" class="block text-sm font-medium text-gray-200">{{ __('Health Condition') }}</label>
                        <select name="health_condition" id="health_condition" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md">
                            <option value="none">{{ __('None') }}</option>
                            <option value="diabetes">{{ __('Diabetes') }}</option>
                            <option value="hypertension">{{ __('Hypertension') }}</option>
                            <option value="heart_disease">{{ __('Heart Disease') }}</option>
                            <option value="celiac">{{ __('Celiac Disease') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="share_to_social" class="block text-sm font-medium text-gray-200">{{ __('Share to Social Media') }}</label>
                        <select name="platforms[]" id="share_to_social" multiple class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md">
                            <option value="instagram">{{ __('Instagram') }}</option>
                            <option value="facebook">{{ __('Facebook') }}</option>
                            <option value="youtube">{{ __('YouTube') }}</option>
                        </select>
                        <p class="text-gray-400 text-xs mt-1">{{ __('Hold Ctrl (Windows) or Cmd (Mac) to select multiple') }}</p>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3 flex justify-end items-center gap-4">
                        <div id="uploadLoading" class="hidden">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 border-4 border-t-transparent border-pink-400 rounded-full animate-spin"></div>
                                <span class="text-gray-200">{{ __('Analyzing...') }}</span>
                            </div>
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-pink-600 to-purple-600 text-white px-8 py-3 rounded-lg hover:from-pink-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">{{ __('Analyze with NutriSnap') }}</button>
                    </div>
                </div>
            </form>

            <!-- Real-Time Analysis Result (Only shown if meal exists) -->
            @if (session('meal') && is_array(session('meal')) && !empty(session('meal')))
                <div id="analysisResult" class="mt-6 bg-gray-800/80 p-6 rounded-2xl shadow-lg animate-fadeIn">
                    <h3 class="text-xl font-semibold text-white mb-4 border-b border-gray-700 pb-2">{{ __('NutriSnap Analysis') }}</h3>
                    @if (session('meal.is_non_food'))
                        <div id="nonFoodWarning" class="bg-red-900/80 p-4 rounded-lg mb-4">
                            <p class="text-red-400 font-medium">{{ __('Warning: No food detected in the image. Detected items: :items', ['items' => implode(', ', session('meal.analysis.non_food_items', []))]) }}</p>
                        </div>
                    @endif
                    <div id="resultContent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <p class="flex justify-between"><strong class="text-gray-200">{{ __('Food Items') }}:</strong> <span id="resultFood" class="text-gray-300">{{ session('meal.analysis.food', 'N/A') }}</span></p>
                            <p class="flex justify-between"><strong class="text-gray-200">{{ __('Calories') }}:</strong> <span id="resultCalories" class="text-gray-300">{{ session('meal.calories', 'N/A') }} kcal</span></p>
                            <p class="flex justify-between"><strong class="text-gray-200">{{ __('Source') }}:</strong> <span id="resultSource" class="text-gray-300">{{ session('meal.analysis.source', 'N/A') }}</span></p>
                            <p class="flex justify-between"><strong class="text-gray-200">{{ __('Image Cropped') }}:</strong> <span id="resultWasCropped" class="text-gray-300">{{ session('meal.analysis.was_cropped') ? __('Yes') : __('No') }}</span></p>
                            <p class="flex justify-between"><strong class="text-gray-200">{{ __('Feedback') }}:</strong> <span id="resultFeedback" class="text-gray-300">{{ session('meal.feedback', 'N/A') }}</span></p>
                        </div>
                        @if (!session('meal.is_non_food') && session('meal.analysis.macronutrients'))
                            <div id="resultMacronutrients">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Macronutrients') }}:</p>
                                <div class="relative w-32 h-32 mx-auto">
                                    <canvas id="macroChart"></canvas>
                                </div>
                                <ul id="macroList" class="list-disc pl-5 text-gray-300 space-y-1 mt-2">
                                    @foreach (session('meal.analysis.macronutrients', []) as $key => $value)
                                        <li>{{ ucfirst($key) }}: {{ $value['value'] ?? 0 }}{{ $value['unit'] ?? 'g' }} ({{ $value['percentage'] ?? 0 }}%)</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (!session('meal.is_non_food') && session('meal.analysis.micronutrients'))
                            <div id="resultMicronutrients">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Micronutrients') }}:</p>
                                <ul id="micronutrientList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach (session('meal.analysis.micronutrients', []) as $key => $value)
                                        <li>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value['value'] ?? 0 }}{{ $value['unit'] ?? '' }} ({{ $value['percentage'] ?? 0 }}% DV)</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (!session('meal.is_non_food') && session('meal.analysis.nutrients'))
                            <div id="resultNutrients">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Nutrients') }}:</p>
                                <ul id="nutrientList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach (session('meal.analysis.nutrients', []) as $key => $value)
                                        <li>{{ ucfirst($key) }}: {{ $value ?? 0 }}g</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (!session('meal.is_non_food') && session('meal.analysis.ingredients'))
                            <div id="resultIngredients">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Ingredients') }}:</p>
                                <ul id="ingredientList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach (session('meal.analysis.ingredients', []) as $item)
                                        <li>{{ $item['name'] ?? $item ?? 'Unknown' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('meal.is_non_food') && session('meal.analysis.non_food_items'))
                            <div id="resultNonFoodItems">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Non-Food Items') }}:</p>
                                <ul id="nonFoodItemsList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach (session('meal.analysis.non_food_items', []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (!session('meal.is_non_food') && session('meal.analysis.preparation'))
                            <div id="resultPreparation">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Preparation') }}:</p>
                                <ul id="preparationList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach (session('meal.analysis.preparation', []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (!session('meal.is_non_food') && session('meal.analysis.serving_details'))
                            <div id="resultServingDetails">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Serving Details') }}:</p>
                                <ul id="servingDetailsList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    <li>{{ __('Method') }}: {{ session('meal.analysis.serving_details.method', 'N/A') }}</li>
                                    <li>{{ __('Utensils') }}: {{ implode(', ', session('meal.analysis.serving_details.utensils', ['N/A'])) }}</li>
                                    <li>{{ __('Setting') }}: {{ session('meal.analysis.serving_details.setting', 'N/A') }}</li>
                                </ul>
                            </div>
                        @endif
                        @if (!session('meal.is_non_food') && session('meal.analysis.suitability'))
                            <div id="resultSuitability">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Dietary Suitability') }}:</p>
                                <ul id="suitabilityList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach (session('meal.analysis.suitability', []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('meal.analysis.recommendations'))
                            <div id="resultRecommendations" class="sm:col-span-2 lg:col-span-3">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Food Recommendations') }}:</p>
                                <ul id="recommendationList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach (session('meal.analysis.recommendations', []) as $rec)
                                        <li>{{ $rec }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('meal.analysis.health_warnings'))
                            <div id="resultHealthWarnings" class="sm:col-span-2 lg:col-span-3">
                                <p class="text-gray-200 font-medium mb-2">{{ __('Health Warnings') }}:</p>
                                <ul id="healthWarningsList" class="list-disc pl-5 text-gray-300 space-y-1">
                                    @foreach (session('meal.analysis.health_warnings', []) as $warning)
                                        <li>{{ $warning }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('meal.share_link'))
                            <div id="resultShare" class="sm:col-span-2 lg:col-span-3">
                                <p class="text-gray-200 font-medium mb-4">{{ __('Share to Earn Benefits') }}:</p>
                                <div id="shareLinks" class="flex flex-wrap gap-4">
                                    @foreach (session('meal.share_link', []) as $platform => $url)
                                        <a href="{{ $url }}" target="_blank" class="px-4 py-2 rounded-lg text-white font-medium {{ $platform === 'facebook' ? 'bg-blue-600 hover:bg-blue-700' : ($platform === 'instagram' ? 'bg-pink-600 hover:bg-pink-700' : ($platform === 'youtube' ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-600 hover:bg-gray-700')) }} transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-1">{{ ucfirst($platform) }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div id="shareProofForm" class="sm:col-span-2 lg:col-span-3">
                                <form id="submitShareProof" action="{{ route('meals.shareProof') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="meal_id" value="{{ session('meal.id') }}">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <div>
                                            <label for="share_proof" class="block text-sm font-medium text-gray-200">{{ __('Share Proof URL') }} <span class="text-red-400">*</span></label>
                                            <input type="url" name="share_proof" id="share_proof" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" placeholder="{{ __('Paste your post URL') }}" required>
                                        </div>
                                        <div>
                                            <label for="platform" class="block text-sm font-medium text-gray-200">{{ __('Platform') }} <span class="text-red-400">*</span></label>
                                            <select name="platform" id="platform" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" required>
                                                <option value="" disabled selected>{{ __('Select Platform') }}</option>
                                                <option value="instagram">{{ __('Instagram') }}</option>
                                                <option value="facebook">{{ __('Facebook') }}</option>
                                                <option value="youtube">{{ __('YouTube') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-pink-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">{{ __('Submit Share Proof') }}</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="flex justify-end mt-4">
                        <button id="clearResults" class="text-pink-400 hover:text-pink-500 font-medium transition-colors duration-200">{{ __('Clear Results') }}</button>
                    </div>
                </div>
            @endif
        </div>

        @if ($meals->isNotEmpty())
            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn">
                <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2">{{ __('Upload Leftover Photo') }}</h3>
                <form id="leftoverUploadForm" action="{{ route('meals.leftover') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="meal_id" class="block text-sm font-medium text-gray-200">{{ __('Select Meal') }} <span class="text-red-400">*</span></label>
                            <select name="meal_id" id="meal_id" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" required>
                                <option value="" disabled selected>{{ __('Select a Meal') }}</option>
                                @foreach ($meals as $meal)
                                    <option value="{{ $meal->id }}">{{ ucfirst($meal->meal_type) }} - {{ $meal->created_at && \Carbon\Carbon::parse($meal->created_at)->isValid() ? \Carbon\Carbon::parse($meal->created_at)->setTimezone('Africa/Lagos')->format('M d, Y H:i') : __('Date Unavailable') }}</option>
                                @endforeach
                            </select>
                            @error('meal_id')
                                <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="leftover_photo" class="block text-sm font-medium text-gray-200">{{ __('Leftover Photo') }} <span class="text-red-400">*</span></label>
                            <input type="file" name="leftover_photo" id="leftover_photo" accept="image/*" class="mt-2 block w-full border-gray-600 rounded-lg bg-gray-700 text-white p-3 file:bg-gradient-to-r file:from-pink-600 file:to-purple-600 file:text-white file:border-none file:rounded-lg file:px-4 file:py-2 focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 shadow-sm hover:shadow-md" required>
                            <div id="leftoverPhotoPreview" class="mt-2 hidden">
                                <img src="" alt="{{ __('Leftover Photo Preview') }}" class="w-32 h-32 object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105 cursor-pointer">
                            </div>
                            @error('leftover_photo')
                                <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2 flex justify-end items-center gap-4">
                            <div id="leftoverLoading" class="hidden">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 border-4 border-t-transparent border-pink-400 rounded-full animate-spin"></div>
                                    <span class="text-gray-200">{{ __('Analyzing...') }}</span>
                                </div>
                            </div>
                            <button type="submit" class="bg-gradient-to-r from-pink-600 to-purple-600 text-white px-8 py-3 rounded-lg hover:from-pink-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">{{ __('Analyze Leftover') }}</button>
                        </div>
                    </div>
                </form>

                <!-- Leftover Analysis Result (Only shown if leftover meal exists) -->
                @if (session('leftover_meal') && is_array(session('leftover_meal')) && !empty(session('leftover_meal')))
                    <div id="leftoverAnalysisResult" class="mt-6 bg-gray-800/80 p-6 rounded-2xl shadow-lg animate-fadeIn">
                        <h3 class="text-xl font-semibold text-white mb-4 border-b border-gray-700 pb-2">{{ __('Leftover Analysis') }}</h3>
                        @if (session('leftover_meal.is_non_food'))
                            <div id="leftoverNonFoodWarning" class="bg-red-900/80 p-4 rounded-lg mb-4">
                                <p class="text-red-400 font-medium">{{ __('Warning: No food detected in the image. Detected items: :items', ['items' => implode(', ', session('leftover_meal.analysis.non_food_items', []))]) }}</p>
                            </div>
                        @endif
                        <div id="leftoverResultContent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <p class="flex justify-between"><strong class="text-gray-200">{{ __('Calories Consumed') }}:</strong> <span id="leftoverResultCalories" class="text-gray-300">{{ session('leftover_meal.calories', 'N/A') }} kcal</span></p>
                                <p class="flex justify-between"><strong class="text-gray-200">{{ __('Feedback') }}:</strong> <span id="leftoverResultFeedback" class="text-gray-300">{{ session('leftover_meal.feedback', 'N/A') }}</span></p>
                                <p class="flex justify-between"><strong class="text-gray-200">{{ __('Source') }}:</strong> <span id="leftoverResultSource" class="text-gray-300">{{ session('leftover_meal.analysis.source', 'N/A') }}</span></p>
                                <p class="flex justify-between"><strong class="text-gray-200">{{ __('Image Cropped') }}:</strong> <span id="leftoverResultWasCropped" class="text-gray-300">{{ session('leftover_meal.analysis.was_cropped') ? __('Yes') : __('No') }}</span></p>
                            </div>
                            @if (!session('leftover_meal.is_non_food') && session('leftover_meal.analysis.macronutrients'))
                                <div id="leftoverResultMacronutrients">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Macronutrients') }}:</p>
                                    <div class="relative w-32 h-32 mx-auto">
                                        <canvas id="leftoverMacroChart"></canvas>
                                    </div>
                                    <ul id="leftoverMacroList" class="list-disc pl-5 text-gray-300 space-y-1 mt-2">
                                        @foreach (session('leftover_meal.analysis.macronutrients', []) as $key => $value)
                                            <li>{{ ucfirst($key) }}: {{ $value['value'] ?? 0 }}{{ $value['unit'] ?? 'g' }} ({{ $value['percentage'] ?? 0 }}%)</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!session('leftover_meal.is_non_food') && session('leftover_meal.analysis.micronutrients'))
                                <div id="leftoverResultMicronutrients">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Micronutrients') }}:</p>
                                    <ul id="leftoverMicronutrientList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach (session('leftover_meal.analysis.micronutrients', []) as $key => $value)
                                            <li>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value['value'] ?? 0 }}{{ $value['unit'] ?? '' }} ({{ $value['percentage'] ?? 0 }}% DV)</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!session('leftover_meal.is_non_food') && !empty(session('leftover_meal.analysis.nutrients')))
                                <div id="leftoverResultNutrients">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Nutrients') }}:</p>
                                    <ul id="leftoverNutrientList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach (session('leftover_meal.analysis.nutrients', []) as $key => $value)
                                            <li>{{ ucfirst($key) }}: {{ number_format($value ?? 0, 2) }}g</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!session('leftover_meal.is_non_food') && !empty(session('leftover_meal.analysis.ingredients')))
                                <div id="leftoverResultIngredients">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Ingredients') }}:</p>
                                    <ul id="leftoverIngredientList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach (session('leftover_meal.analysis.ingredients', []) as $item)
                                            <li>{{ is_array($item) ? ($item['name'] ?? 'Unknown') : ($item ?? 'Unknown') }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session('leftover_meal.is_non_food') && !empty(session('leftover_meal.analysis.non_food_items')))
                                <div id="leftoverResultNonFoodItems">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Non-Food Items') }}:</p>
                                    <ul id="leftoverNonFoodItemsList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach (session('leftover_meal.analysis.non_food_items', []) as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!session('leftover_meal.is_non_food') && !empty(session('leftover_meal.analysis.preparation')))
                                <div id="leftoverResultPreparation">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Preparation') }}:</p>
                                    <ul id="leftoverPreparationList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach (session('leftover_meal.analysis.preparation', []) as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!session('leftover_meal.is_non_food') && !empty(session('leftover_meal.analysis.serving_details')))
                                <div id="leftoverResultServingDetails">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Serving Details') }}:</p>
                                    <ul id="leftoverServingDetailsList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        <li>{{ __('Method') }}: {{ session('leftover_meal.analysis.serving_details.method', 'N/A') }}</li>
                                        <li>{{ __('Utensils') }}: {{ implode(', ', session('leftover_meal.analysis.serving_details.utensils', ['N/A'])) }}</li>
                                        <li>{{ __('Setting') }}: {{ session('leftover_meal.analysis.serving_details.setting', 'N/A') }}</li>
                                    </ul>
                                </div>
                            @endif
                            @if (!session('leftover_meal.is_non_food') && !empty(session('leftover_meal.analysis.suitability')))
                                <div id="leftoverResultSuitability">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Dietary Suitability') }}:</p>
                                    <ul id="leftoverSuitabilityList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach (session('leftover_meal.analysis.suitability', []) as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!empty(session('leftover_meal.analysis.recommendations')))
                                <div id="leftoverResultRecommendations" class="sm:col-span-2 lg:col-span-3">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Food Recommendations') }}:</p>
                                    <ul id="leftoverRecommendationList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach (session('leftover_meal.analysis.recommendations', []) as $rec)
                                            <li>{{ $rec }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!empty(session('leftover_meal.analysis.health_warnings')))
                                <div id="leftoverResultHealthWarnings" class="sm:col-span-2 lg:col-span-3">
                                    <p class="text-gray-200 font-medium mb-2">{{ __('Health Warnings') }}:</p>
                                    <ul id="leftoverHealthWarningsList" class="list-disc pl-5 text-gray-300 space-y-1">
                                        @foreach (session('leftover_meal.analysis.health_warnings', []) as $warning)
                                            <li>{{ $warning }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Filters -->
        <div class="flex flex-wrap items-center justify-end gap-4 mb-6">
            <a href="{{ route('meals.index', ['period' => '1week']) }}" class="pill {{ $period === '1week' ? 'pill-active' : '' }}">{{ __('1 Week') }}</a>
            <a href="{{ route('meals.index', ['period' => '1month']) }}" class="pill {{ $period === '1month' ? 'pill-active' : '' }}">{{ __('1 Month') }}</a>
            <a href="{{ route('meals.index', ['period' => '3months']) }}" class="pill {{ $period === '3months' ? 'pill-active' : '' }}">{{ __('3 Months') }}</a>
        </div>

        <!-- Calories Chart (Only shown if there is data) -->
        @if (!empty($chartData))
            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn">
                <h3 class="text-2xl font-semibold text-white mb-6 border-b border-gray-700 pb-2">{{ __('Calorie Overview') }} ({{ $period === '1week' ? __('Last 7 Days') : ($period === '3months' ? __('Last 3 Months') : __('Last 30 Days')) }})</h3>
                <div class="relative w-full" style="height: 350px;">
                    <canvas id="calorieChart"></canvas>
                </div>
            </div>
        @else
            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg mb-10 animate-fadeIn">
                <p class="text-gray-400 text-center text-base">{{ __('No calorie data available for the selected period. Upload a meal to start tracking!') }}</p>
            </div>
        @endif

        <!-- Meals Grid -->
        <div class="bg-gray-900 p-4 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-white mb-3">{{ __('Meal History') }}</h3>
            <div id="mealsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @if ($meals->isEmpty())
                    <p class="text-gray-400 text-base text-center col-span-full">{{ __('No meals uploaded.') }} <a href="" class="text-pink-400 hover:text-pink-500">{{ __('Upload now!') }}</a></p>
                @else
                    @foreach ($meals->take(12) as $meal)
                        <div class="meal-card bg-gray-800 rounded-md p-3 hover:shadow-md transition-all duration-200" data-meal-id="{{ $meal->id }}">
                            <div class="flex flex-col space-y-2">
                                <div class="flex space-x-2">
                                    @if ($meal->photo_url && $meal->photo_exists)
                                        <a href="{{ route('meals.show', $meal->id) }}" class="group">
                                            <img src="{{ $meal->photo_url }}" alt="{{ __('Meal') }}" class="w-24 h-24 sm:w-20 sm:h-20 object-contain rounded-sm group-hover:scale-105 transition-transform duration-200 antialiased max-w-full" loading="lazy" onerror="this.src='{{ asset('images/nutrisnap-logo.png') }}';">
                                        </a>
                                    @else
                                        <img src="{{ asset('images/nutrisnap-logo.png') }}" alt="{{ __('Default') }}" class="w-24 h-24 sm:w-20 sm:h-20 object-contain rounded-sm antialiased max-w-full" loading="lazy">
                                    @endif
                                    @if ($meal->leftover_photo_url && $meal->leftover_photo_exists)
                                        <a href="{{ route('meals.show', $meal->id) }}" class="group">
                                            <img src="{{ $meal->leftover_photo_url }}" alt="{{ __('Leftover') }}" class="w-24 h-24 sm:w-20 sm:h-20 object-contain rounded-sm group-hover:scale-105 transition-transform duration-200 antialiased max-w-full" loading="lazy" onerror="this.src='{{ asset('images/nutrisnap-logo.png') }}';">
                                        </a>
                                    @endif
                                </div>
                                <div class="text-sm flex justify-between">
                                    <span class="text-pink-400">{{ __('ID') }}:</span>
                                    <span class="text-gray-300">{{ $meal->id }}</span>
                                </div>
                                <div class="text-sm flex justify-between">
                                    <span class="text-pink-400">{{ __('Type') }}:</span>
                                    <span class="text-gray-300">{{ ucfirst($meal->meal_type ?? 'N/A') }}</span>
                                </div>
                                <div class="text-sm flex justify-between">
                                    <span class="text-pink-400">{{ __('Calories') }}:</span>
                                    <span class="text-gray-300">{{ $meal->analysis['calories'] ?? 'N/A' }} kcal</span>
                                </div>
                                @if ($meal->analysis['is_non_food'] ?? false)
                                    <div class="text-sm flex justify-between">
                                        <span class="text-pink-400">{{ __('Items') }}:</span>
                                        <span class="text-gray-300">{{ !empty($meal->analysis['non_food_items']) ? implode(', ', $meal->analysis['non_food_items']) : 'None' }}</span>
                                    </div>
                                @else
                                    <div class="text-sm flex justify-between">
                                        <span class="text-pink-400">{{ __('Food') }}:</span>
                                        <span class="text-gray-300">
                                            @if (!empty($meal->analysis['food_items']) && is_array($meal->analysis['food_items']))
                                                {{ collect($meal->analysis['food_items'])->map(function ($prob, $food) {
                                                    return "$food (" . number_format($prob * 100, 1) . "%)";
                                                })->implode(', ') ?: 'None' }}
                                            @else
                                                {{ $meal->analysis['food'] ?? 'None' }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                                <div class="text-sm flex justify-between relative group">
                                    <span class="text-pink-400">{{ __('Feedback') }}:</span>
                                    <span class="text-gray-300 truncate max-w-[8rem]">{{ Str::limit($meal->feedback ?? 'N/A', 20) }}</span>
                                    <span class="absolute hidden group-hover:block bg-gray-700 text-white text-sm rounded px-2 py-1 -top-8">{{ $meal->feedback ?? 'N/A' }}</span>
                                </div>
                                <div class="text-sm flex justify-between">
                                    <span class="text-pink-400">{{ __('Status') }}:</span>
                                    <span class="text-gray-300 {{ $meal->status === 'pending' ? 'text-yellow-400' : ($meal->status === 'non_food_detected' ? 'text-red-400' : 'text-green-400') }}">{{ ucfirst(str_replace('_', ' ', $meal->status ?? 'N/A')) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <button class="toggle-analysis text-pink-400 hover:text-pink-500 text-sm" data-meal-id="{{ $meal->id }}">{{ __('Details') }}</button>
                                    <div class="flex space-x-2">
                                        @if ($meal->uuid)
                                            <a href="{{ route('results.public', $meal->uuid) }}" class="text-pink-400 hover:text-pink-500" title="{{ __('Share') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                            </a>
                                        @endif
                                        <a href="{{ route('meals.show', $meal->id) }}" class="text-pink-400 hover:text-pink-500" title="{{ __('View') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <form action="{{ route('meals.destroy', $meal->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-500" title="{{ __('Delete') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0H7a2 2 0 00-2 2v2h14V5a2 2 0 00-2-2h-3m-4 0V3h4v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @include('meals.partials.analysis-details', ['meal' => $meal])
                        </div>
                    @endforeach
                    @if ($meals->count() > 12)
                        <div class="col-span-full text-center mt-3">
                            <a href="{{ route('meals.index', ['period' => $period]) }}" class="text-pink-400 hover:text-pink-500 text-base">{{ __('Load More') }}</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
            // Pass translations to JavaScript
            const translations = {
                protein: '{{ __('Protein') }}',
                carbohydrates: '{{ __('Carbohydrates') }}',
                fat: '{{ __('Fat') }}',
                daily_calories: '{{ __('Daily Calories (kcal)') }}',
                date: '{{ __('Date') }}',
                calories_kcal: '{{ __('Calories (kcal)') }}',
                share_copied: '{{ __('Share link copied to clipboard!') }}',
                share_failed: '{{ __('Failed to copy share link.') }}',
                show_details: '{{ __('Show Details') }}',
                hide_details: '{{ __('Hide Details') }}'
            };

            document.addEventListener('DOMContentLoaded', function () {
                const photoInput = document.getElementById('photo');
                const photoPreview = document.getElementById('photoPreview');
                const leftoverPhotoInput = document.getElementById('leftover_photo');
                const leftoverPhotoPreview = document.getElementById('leftoverPhotoPreview');
                const analysisResult = document.getElementById('analysisResult');
                const leftoverAnalysisResult = document.getElementById('leftoverAnalysisResult');
                const mealsGrid = document.getElementById('mealsGrid');
                let macroChart = null;
                let leftoverMacroChart = null;
                const mealMacroCharts = {};
                const chartData = @json($chartData);

                console.log('DOMContentLoaded fired. Initializing NutriSnap scripts...');
                console.log('Chart Data:', chartData);
                console.log('Meals:', @json($meals));

                // Image Preview
                function handleImagePreview(input, preview) {
                    console.log('Setting up image preview for:', input.id);
                    input.addEventListener('change', function () {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                preview.querySelector('img').src = e.target.result;
                                preview.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);
                        } else {
                            preview.classList.add('hidden');
                        }
                    });
                }

                handleImagePreview(photoInput, photoPreview);
                handleImagePreview(leftoverPhotoInput, leftoverPhotoPreview);

                // Initialize Macronutrient Chart
                function initializeMacroChart(canvasId, data) {
                    const ctx = document.getElementById(canvasId)?.getContext('2d');
                    if (!ctx) {
                        console.error(`Canvas element #${canvasId} not found`);
                        return null;
                    }

                    try {
                        const chartData = {
                            labels: [translations.protein, translations.carbohydrates, translations.fat],
                            datasets: [{
                                data: [
                                    data?.protein?.percentage || data?.protein?.value || 0,
                                    data?.carbs?.percentage || data?.carbs?.value || 0,
                                    data?.fat?.percentage || data?.fat?.value || 0
                                ],
                                backgroundColor: ['#ec4899', '#8b5cf6', '#3b82f6'],
                                borderColor: '#1f2937',
                                borderWidth: 2,
                            }]
                        };
                        const chart = new Chart(ctx, {
                            type: 'doughnut',
                            data: chartData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom', labels: { color: '#f3f4f6', font: { size: 12 } } },
                                    tooltip: {
                                        backgroundColor: '#1f2937',
                                        titleColor: '#f3f4f6',
                                        bodyColor: '#d1d5db',
                                        callbacks: { label: function(context) { return `${context.label}: ${context.raw}%`; } }
                                    },
                                },
                            },
                        });
                        console.log(`Macronutrient chart ${canvasId} initialized successfully`);
                        return chart;
                    } catch (error) {
                        console.error(`Macronutrient chart ${canvasId} initialization failed:`, error);
                        return null;
                    }
                }

                // Initialize Meal Macronutrient Charts
                function initializeMealMacroCharts() {
                    @foreach ($meals as $meal)
                        @if (!empty($meal->analysis['macronutrients']) && !$meal->analysis['is_non_food'])
                            mealMacroCharts['{{ $meal->id }}'] = initializeMacroChart('macroChart-{{ $meal->id }}', @json($meal->analysis['macronutrients']));
                        @endif
                        @if (!empty($meal->leftover_analysis['macronutrients']) && !$meal->leftover_analysis['is_non_food'])
                            mealMacroCharts['leftover-{{ $meal->id }}'] = initializeMacroChart('leftoverMacroChart-{{ $meal->id }}', @json($meal->leftover_analysis['macronutrients']));
                        @endif
                    @endforeach
                    @if (session('meal.analysis.macronutrients') && !session('meal.is_non_food'))
                        macroChart = initializeMacroChart('macroChart', @json(session('meal.analysis.macronutrients')));
                    @endif
                    @if (session('leftover_meal.analysis.macronutrients') && !session('leftover_meal.is_non_food'))
                        leftoverMacroChart = initializeMacroChart('leftoverMacroChart', @json(session('leftover_meal.analysis.macronutrients')));
                    @endif
                }

                // Toggle Analysis Details
                function toggleAnalysisDetails(event) {
                    const button = event.target;
                    const mealId = button.getAttribute('data-meal-id');
                    const analysisDiv = document.querySelector(`.analysis-details[data-meal-id="${mealId}"]`);
                    if (analysisDiv) {
                        analysisDiv.classList.toggle('hidden');
                        button.textContent = analysisDiv.classList.contains('hidden') ? translations.show_details : translations.hide_details;
                    }
                }

                // Clear Results
                document.getElementById('clearResults')?.addEventListener('click', function () {
                    console.log('Clearing analysis results');
                    if (analysisResult) analysisResult.classList.add('hidden');
                    if (leftoverAnalysisResult) leftoverAnalysisResult.classList.add('hidden');
                    if (macroChart) {
                        macroChart.destroy();
                        macroChart = null;
                    }
                    if (leftoverMacroChart) {
                        leftoverMacroChart.destroy();
                        leftoverMacroChart = null;
                    }
                });

                // Show Loading on Form Submit
                const uploadForm = document.getElementById('uploadForm');
                const uploadLoading = document.getElementById('uploadLoading');
                if (uploadForm && uploadLoading) {
                    uploadForm.addEventListener('submit', function (e) {
                        uploadLoading.classList.remove('hidden');
                        this.querySelector('button[type="submit"]').disabled = true;
                    });
                }

                const leftoverUploadForm = document.getElementById('leftoverUploadForm');
                const leftoverLoading = document.getElementById('leftoverLoading');
                if (leftoverUploadForm && leftoverLoading) {
                    leftoverUploadForm.addEventListener('submit', function (e) {
                        leftoverLoading.classList.remove('hidden');
                        this.querySelector('button[type="submit"]').disabled = true;
                    });
                }

                // Event Listeners for Analysis Toggle
                document.querySelectorAll('.toggle-analysis').forEach(button => {
                    button.addEventListener('click', toggleAnalysisDetails);
                });

                // Share Link Copy
                document.querySelectorAll('.share-btn').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const shareUrl = e.target.getAttribute('href');
                        navigator.clipboard.writeText(shareUrl).then(() => {
                            alert(translations.share_copied);
                        }).catch(err => {
                            console.error('Failed to copy share link:', err);
                            alert(translations.share_failed);
                        });
                    });
                });

                // Initialize Charts
                initializeMealMacroCharts();

                // Calorie Chart (Only initialize if chartData is not empty)
                const ctx = document.getElementById('calorieChart')?.getContext('2d');
                if (ctx && chartData && chartData.length > 0) {
                    try {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: chartData.map(item => item.date || ''),
                                datasets: [{
                                    label: translations.daily_calories,
                                    data: chartData.map(item => item.calories || 0),
                                    borderColor: "#ec4899",
                                    backgroundColor: "rgba(236, 72, 153, 0.2)",
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: "#ffffff",
                                    pointBorderColor: "#ec4899",
                                    pointHoverRadius: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        title: { display: true, text: translations.date, color: "#f3f4f6", font: { size: 14, weight: "500" } },
                                        ticks: { color: "#d1d5db", maxTicksLimit: 10 },
                                        grid: { color: "rgba(209, 213, 219, 0.1)" }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        title: { display: true, text: translations.calories_kcal, color: "#f3f4f6", font: { size: 14, weight: "500" } },
                                        ticks: { color: "#d1d5db", stepSize: 100 },
                                        grid: { color: "rgba(209, 213, 219, 0.1)" }
                                    }
                                },
                                plugins: {
                                    legend: { labels: { color: "#f3f4f6", font: { size: 14 } } },
                                    tooltip: { 
                                        backgroundColor: "#1f2937",
                                        titleColor: "#f3f4f6",
                                        bodyColor: "#d1d5db",
                                        borderColor: "#ec4899",
                                        borderWidth: 1
                                    }
                                }
                            }
                        });
                        console.log('Calorie chart initialized successfully');
                    } catch (error) {
                        console.error('Calorie chart initialization failed:', error);
                    }
                } else {
                    console.log('No calorie data to display or canvas not found');
                }
            });
            
                    window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            setTimeout(() => {
                preloader.classList.add('hidden');
            }, 1000);
        });
        </script>

        <style>
            .pill {
                @apply px-4 py-2 rounded-full text-gray-200 bg-gray-700 hover:bg-gray-600 transition-all duration-300 text-sm font-medium shadow-sm hover:shadow-md;
            }
            .pill-active {
                @apply bg-gradient-to-r from-pink-600 to-purple-600 text-white hover:from-pink-700 hover:to-purple-700;
            }
            .tooltip {
                @apply absolute z-10 bg-gray-900 text-gray-200 text-sm rounded-lg px-3 py-2 shadow-lg -top-10 left-1/2 transform -translate-x-1/2 whitespace-nowrap;
            }
            .meal-image:hover + .tooltip,
            .group:hover .tooltip {
                @apply block;
            }
            .animate-slideUp {
                animation: slideUp 0.5s ease-out;
            }
            .animate-fadeIn {
                animation: fadeIn 0.5s ease-in;
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
                to { transform: rotate(360deg); }
            }
            .animate-spin {
                animation: spin 1s linear infinite;
            }
        </style>
    </div>
@endsection