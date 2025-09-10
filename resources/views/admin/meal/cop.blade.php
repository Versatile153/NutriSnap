@extends('layouts.app2')

@section('title', __('Coupon Details'))

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8 animate-fade-in">
    <h1 class="text-3xl font-bold text-nutri-blue dark:text-blue-400 mb-6">{{ __('Coupon Details') }}</h1>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('ID') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $coupon->id }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Code') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $coupon->code ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Status') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ ucfirst($coupon->status) ?? __('N/A') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Created At') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $coupon->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('Updated At') }}</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $coupon->updated_at->format('Y-m-d H:i') }}</p>
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