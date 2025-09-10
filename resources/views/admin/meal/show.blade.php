@extends('layouts.app2')

@section('title', __('Meal Details'))

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8 animate-fade-in">
    <h1 class="text-3xl font-bold text-nutri-blue dark:text-blue-400 mb-6">{{ __('Meal Details') }}</h1>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('ID') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->id }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('User ID') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->user_id }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Meal Type') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ ucfirst($meal->meal_type) }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Calories') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->calories ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Portion Size') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->portion_size ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Status') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ ucfirst($meal->status) }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Health Condition') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->health_condition ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Platform') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->platform ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('UUID') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->uuid ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Created At') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Updated At') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->updated_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="sm:col-span-2">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Photo') }}</h3>
                <img src="{{ $meal->photo_url ? Storage::url($meal->photo_url) : asset('images/placeholder.jpg') }}" 
                     alt="{{ __('Meal Photo') }}" 
                     class="object-cover w-full h-auto rounded-lg shadow-md" 
                     onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
            </div>
            <div class="sm:col-span-2">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Leftover Photo') }}</h3>
                <img src="{{ $meal->leftover_photo_url ? Storage::url($meal->leftover_photo_url) : asset('images/placeholder.jpg') }}" 
                     alt="{{ __('Leftover Photo') }}" 
                     class="object-cover w-full h-auto rounded-lg shadow-md" 
                     onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
            </div>
            <div class="sm:col-span-2">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Analysis') }}</h3>
                <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg text-sm text-gray-700 dark:text-gray-300 overflow-auto">{{ json_encode($meal->analysis, JSON_PRETTY_PRINT) ?? __('N/A') }}</pre>
            </div>
            <div class="sm:col-span-2">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Leftover Analysis') }}</h3>
                <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg text-sm text-gray-700 dark:text-gray-300 overflow-auto">{{ json_encode($meal->leftover_analysis, JSON_PRETTY_PRINT) ?? __('N/A') }}</pre>
            </div>
            <div class="sm:col-span-2">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Share Link') }}</h3>
                <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg text-sm text-gray-700 dark:text-gray-300 overflow-auto">{{ json_encode($meal->share_link, JSON_PRETTY_PRINT) ?? __('N/A') }}</pre>
            </div>
            <div class="sm:col-span-2">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Share Proof') }}</h3>
                <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg text-sm text-gray-700 dark:text-gray-300 overflow-auto">{{ json_encode($meal->share_proof, JSON_PRETTY_PRINT) ?? __('N/A') }}</pre>
            </div>
            <div class="sm:col-span-2">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Feedback') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->feedback ?? __('N/A') }}</p>
            </div>
            <div class="sm:col-span-2">
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Correction Request') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->correction_request ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Corrected Calories') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->corrected_calories ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Corrected Food') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $meal->corrected_food ?? __('N/A') }}</p>
            </div>
        </div>
        <div class="mt-6">
            <a href="{{ route('admin.analysis') }}" 
               class="inline-block px-4 py-2 bg-nutri-blue text-white rounded-lg hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
               {{ __('Back to Analysis') }}
            </a>
        </div>
    </div>
</div>
@endsection