@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-gray-800 p-6 sm:p-8 rounded-lg shadow-lg transform transition-all duration-300 hover:shadow-xl mt-8">
        <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-white text-center">{{ __('Register') }}</h2>
        @if (session('status'))
            <div class="bg-green-600 text-white p-3 rounded mb-6 text-center" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm sm:text-base font-medium text-gray-300">{{ __('Name') }}</label>
                <div class="relative">
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full p-3 sm:p-4 border rounded-lg border-gray-700 bg-gray-900 text-white focus:ring-2 focus:ring-pink-400 focus:border-transparent placeholder-gray-500">
                    @error('name')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm sm:text-base font-medium text-gray-300">{{ __('Email') }}</label>
                <div class="relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full p-3 sm:p-4 border rounded-lg border-gray-700 bg-gray-900 text-white focus:ring-2 focus:ring-pink-400 focus:border-transparent placeholder-gray-500">
                    @error('email')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm sm:text-base font-medium text-gray-300">{{ __('Password') }}</label>
                <div class="relative">
                    <input id="password" x-ref="password" type="password" name="password" required class="w-full p-3 sm:p-4 border rounded-lg border-gray-700 bg-gray-900 text-white focus:ring-2 focus:ring-pink-400 focus:border-transparent placeholder-gray-500">
                    <button type="button" x-data="{ show: false }" x-on:click="show = !show; $refs.password.type = show ? 'text' : 'password'" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-pink-400">
                        <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                    @error('password')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm sm:text-base font-medium text-gray-300">{{ __('Confirm Password') }}</label>
                <div class="relative">
                    <input id="password_confirmation" x-ref="confirmPassword" type="password" name="password_confirmation" required class="w-full p-3 sm:p-4 border rounded-lg border-gray-700 bg-gray-900 text-white focus:ring-2 focus:ring-pink-400 focus:border-transparent placeholder-gray-500">
                    <button type="button" x-data="{ show: false }" x-on:click="show = !show; $refs.confirmPassword.type = show ? 'text' : 'password'" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-pink-400">
                        <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="w-full bg-pink-600 text-white p-3 sm:p-4 rounded-lg hover:bg-pink-700 transition-colors duration-200">{{ __('Register') }}</button>
        </form>
    </div>
@endsection