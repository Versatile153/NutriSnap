@extends('layouts.app')

@section('content')
    <main class="mx-auto max-w-4xl px-4 sm:px-6 py-10">
        <h1 class="text-3xl font-bold text-white mb-6 text-center">{{ __('Profile Management') }}</h1>

        <!-- Dropdown to filter forms -->
        <div class="mb-8 flex justify-center">
            <select id="form-selector" class="w-full sm:w-1/2 p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-white">
                <option value="all">{{ __('All Forms') }}</option>
                <option value="profile" selected>{{ __('Profile Information') }}</option>
                <option value="password">{{ __('Update Password') }}</option>
                <option value="delete">{{ __('Delete Account') }}</option>
            </select>
        </div>

        <!-- Onboarding Form (Multi-step Wizard) -->
        @if (!auth()->user()->profile)
            <section id="onboarding-form" class="form-section mb-8 p-6 bg-gray-800 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-white mb-4 text-center">{{ __('Complete Your NutriSnap Profile') }}</h2>
                <form x-data="{ step: 1 }" method="POST" action="{{ route('setup.store') }}" class="max-w-3xl mx-auto">
                    @csrf
                    <div x-show="step === 1">
                        <h3 class="text-lg font-medium text-gray-300 mb-4">{{ __('Personal Information') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-300">{{ __('Height (cm)') }} <span class="text-red-500">*</span></label>
                                <input id="height" type="number" name="height" value="{{ old('height') }}" required class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base">
                                @error('height')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-300">{{ __('Weight (kg)') }} <span class="text-red-500">*</span></label>
                                <input id="weight" type="number" name="weight" value="{{ old('weight') }}" required class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base">
                                @error('weight')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="goal" class="block text-sm font-medium text-gray-300">{{ __('Goal') }} <span class="text-red-500">*</span></label>
                                <select id="goal" name="goal" required class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base">
                                    <option value="weight_loss" {{ old('goal') === 'weight_loss' ? 'selected' : '' }}>{{ __('Lose Weight') }}</option>
                                    <option value="maintain" {{ old('goal') === 'maintain' ? 'selected' : '' }}>{{ __('Maintain') }}</option>
                                    <option value="weight_gain" {{ old('goal') === 'weight_gain' ? 'selected' : '' }}>{{ __('Gain Weight') }}</option>
                                </select>
                                @error('goal')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="daily_calories" class="block text-sm font-medium text-gray-300">{{ __('Daily Calorie Goal') }} <span class="text-red-500">*</span></label>
                                <input id="daily_calories" type="number" name="daily_calories" value="{{ old('daily_calories') }}" required class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base">
                                @error('daily_calories')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="button" @click="step = 2" style="background:#BE185D;" class="w-full md:w-auto bg-nutri-pink text-white p-3 rounded hover:bg-pink-600 text-sm sm:text-base">{{ __('Next') }}</button>
                        </div>
                    </div>
                    <div x-show="step === 2">
                        <h3 class="text-lg font-medium text-gray-300 mb-4">{{ __('Health Conditions') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col space-y-2">
                                <label class="text-sm text-gray-300"><input type="checkbox" name="health_conditions[]" value="diabetes" {{ in_array('diabetes', old('health_conditions', [])) ? 'checked' : '' }} class="mr-2"> {{ __('Diabetes') }}</label>
                                <label class="text-sm text-gray-300"><input type="checkbox" name="health_conditions[]" value="hypertension" {{ in_array('hypertension', old('health_conditions', [])) ? 'checked' : '' }} class="mr-2"> {{ __('Hypertension') }}</label>
                            </div>
                        </div>
                        @error('health_conditions')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <div class="flex justify-between mt-6">
                            <button type="button" @click="step = 1" class="w-full md:w-auto bg-gray-500 text-white p-3 rounded hover:bg-gray-600 text-sm sm:text-base">{{ __('Back') }}</button>
                            <button type="button" @click="step = 3" style="background:#BE185D;" class="w-full md:w-auto bg-nutri-pink text-white p-3 rounded hover:bg-pink-600 text-sm sm:text-base">{{ __('Next') }}</button>
                        </div>
                    </div>
                    <div x-show="step === 3">
                        <h3 class="text-lg font-medium text-gray-300 mb-4">{{ __('Subscription Plan') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col space-y-2">
                                <label class="text-sm text-gray-300"><input type="radio" name="plan" value="free" {{ old('plan') === 'free' ? 'checked' : '' }} checked class="mr-2"> {{ __('Free Plan') }}</label>
                                <label class="text-sm text-gray-300"><input type="radio" name="plan" value="plus" {{ old('plan') === 'plus' ? 'checked' : '' }} class="mr-2"> {{ __('Plus ($9.99/mo)') }}</label>
                                <label class="text-sm text-gray-300"><input type="radio" name="plan" value="pro" {{ old('plan') === 'pro' ? 'checked' : '' }} class="mr-2"> {{ __('Pro ($19.99/mo)') }}</label>
                            </div>
                            <div>
                                <label for="coupon_code" class="block text-sm font-medium text-gray-300">{{ __('Coupon Code (Optional)') }}</label>
                                <input id="coupon_code" name="coupon_code" type="text" value="{{ old('coupon_code') }}" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base" placeholder="{{ __('Enter coupon code') }}">
                                @error('coupon_code')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @error('plan')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <div class="flex justify-between mt-6">
                            <button type="button" @click="step = 2" class="w-full md:w-auto bg-gray-500 text-white p-3 rounded hover:bg-gray-600 text-sm sm:text-base">{{ __('Back') }}</button>
                            <button type="submit" style="background:#BE185D;" class="w-full md:w-auto bg-nutri-pink text-white p-3 rounded hover:bg-pink-600 text-sm sm:text-base">{{ __('Save Profile') }}</button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-400 mt-4 text-center">{{ __('Required fields are marked with *') }} {{ __('Your data is securely stored and used to personalize your experience, in compliance with GDPR') }}</p>
                </form>
            </section>
        @endif

        <!-- Profile Information Form -->
        <section id="profile-form" class="form-section mb-8 p-6 bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-white mb-4 text-center">{{ __('Profile Information') }}</h2>
            <p class="text-sm text-gray-400 mb-4 text-center">{{ __('Update your account\'s profile information, email address, and subscription plan') }}</p>
            <form method="POST" action="{{ route('profile.update') }}" class="max-w-3xl mx-auto" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <input type="hidden" name="email_notifications" value="0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300">{{ __('Name') }} <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base" autocomplete="name">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300">{{ __('Email') }} <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base" autocomplete="username">
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <p class="text-sm text-gray-400 mt-2">
                                {{ __('Your email address is unverified') }}
                                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="underline text-gray-300 hover:text-nutri-pink text-sm">{{ __('Click here to re-send the verification email') }}</button>
                                </form>
                            </p>
                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 text-sm text-green-500">{{ __('A new verification link has been sent to your email address') }}</p>
                            @endif
                        @endif
                    </div>
                    @if ($user->profile)
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-300">{{ __('Height (cm)') }}</label>
                            <input id="height" type="number" name="height" value="{{ old('height', $user->profile->height) }}" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base">
                            @error('height')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-300">{{ __('Weight (kg)') }}</label>
                            <input id="weight" type="number" name="weight" value="{{ old('weight', $user->profile->weight) }}" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base">
                            @error('weight')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="goal" class="block text-sm font-medium text-gray-300">{{ __('Goal') }}</label>
                            <select id="goal" name="goal" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base">
                                <option value="weight_loss" {{ old('goal', $user->profile->goal) === 'weight_loss' ? 'selected' : '' }}>{{ __('Lose Weight') }}</option>
                                <option value="maintain" {{ old('goal', $user->profile->goal) === 'maintain' ? 'selected' : '' }}>{{ __('Maintain') }}</option>
                                <option value="weight_gain" {{ old('goal', $user->profile->goal) === 'weight_gain' ? 'selected' : '' }}>{{ __('Gain Weight') }}</option>
                            </select>
                            @error('goal')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="daily_calories" class="block text-sm font-medium text-gray-300">{{ __('Daily Calorie Goal') }}</label>
                            <input id="daily_calories" type="number" name="daily_calories" value="{{ old('daily_calories', $user->profile->daily_calories) }}" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base">
                            @error('daily_calories')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300">{{ __('Health Conditions') }}</label>
                            <div class="flex flex-col space-y-2 mt-2">
                                <label class="text-sm text-gray-300">
                                    <input type="checkbox" name="health_conditions[]" value="diabetes" {{ in_array('diabetes', old('health_conditions', is_array($user->profile->conditions) ? $user->profile->conditions : json_decode($user->profile->conditions ?? '[]', true))) ? 'checked' : '' }} class="mr-2"> {{ __('Diabetes') }}
                                </label>
                                <label class="text-sm text-gray-300">
                                    <input type="checkbox" name="health_conditions[]" value="hypertension" {{ in_array('hypertension', old('health_conditions', is_array($user->profile->conditions) ? $user->profile->conditions : json_decode($user->profile->conditions ?? '[]', true))) ? 'checked' : '' }} class="mr-2"> {{ __('Hypertension') }}
                                </label>
                            </div>
                            @error('health_conditions')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300">{{ __('Subscription Plan') }}</label>
                            <div class="flex flex-col space-y-2 mt-2">
                                <label class="text-sm text-gray-300">
                                    <input type="radio" name="plan" value="free" {{ old('plan', $user->profile->plan) === 'free' ? 'checked' : '' }} class="mr-2"> {{ __('Free Plan') }}
                                </label>
                                <label class="text-sm text-gray-300">
                                    <input type="radio" name="plan" value="plus" {{ old('plan', $user->profile->plan) === 'plus' ? 'checked' : '' }} class="mr-2"> {{ __('Plus ($9.99/mo)') }}
                                </label>
                                <label class="text-sm text-gray-300">
                                    <input type="radio" name="plan" value="pro" {{ old('plan', $user->profile->plan) === 'pro' ? 'checked' : '' }} class="mr-2"> {{ __('Pro ($19.99/mo)') }}
                                </label>
                                <div class="mt-2">
                                    <label for="coupon_code_profile" class="block text-sm font-medium text-gray-300">{{ __('Coupon Code (Optional)') }}</label>
                                    <input id="coupon_code_profile" name="coupon_code" type="text" value="{{ old('coupon_code') }}" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base" placeholder="{{ __('Enter coupon code') }}">
                                    @error('coupon_code')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @error('plan')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300">{{ __('Email Notifications') }}</label>
                        <input type="checkbox" name="email_notifications" value="1" {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }} class="mt-2 mr-2">
                        @error('email_notifications')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" style="background:#BE185D;" class="w-full md:w-auto bg-nutri-pink text-white p-3 rounded hover:bg-pink-600 text-sm sm:text-base">{{ __('Save') }}</button>
                </div>
            </form>
        </section>

        <!-- Update Password Form -->
        <section id="password-form" class="form-section mb-8 p-6 bg-gray-800 rounded-lg shadow-md hidden">
            <h2 class="text-xl font-semibold text-white mb-4 text-center">{{ __('Update Password') }}</h2>
            <p class="text-sm text-gray-400 mb-4 text-center">{{ __('Ensure your account is using a long, random password to stay secure') }}</p>
            <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-md mx-auto">
                @csrf
                @method('put')
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-300">{{ __('Current Password') }}</label>
                    <input id="current_password" name="current_password" type="password" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base" autocomplete="current-password">
                    @error('current_password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">{{ __('New Password') }}</label>
                    <input id="password" name="password" type="password" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base" autocomplete="new-password">
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base" autocomplete="new-password">
                    @error('password_confirmation')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" style="background:#BE185D;" class="w-full md:w-auto bg-nutri-pink text-white p-3 rounded hover:bg-pink-600 text-sm sm:text-base">{{ __('Save') }}</button>
                </div>
                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-500 text-center">{{ __('Saved') }}</p>
                @endif
            </form>
        </section>

        <!-- Delete Account Form -->
        <section id="delete-form" class="form-section mb-8 p-6 bg-gray-800 rounded-lg shadow-md hidden">
            <h2 class="text-xl font-semibold text-white mb-4 text-center">{{ __('Delete Account') }}</h2>
            <p class="text-sm text-gray-400 mb-4 text-center">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted') }} {{ __('Before deleting your account, please download any data or information that you wish to retain') }}</p>
            <div class="flex justify-center">
                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="w-full md:w-auto bg-red-600 text-white p-3 rounded hover:bg-red-700 text-sm sm:text-base">{{ __('Delete Account') }}</button>
            </div>

            <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6 max-w-md mx-auto">
                    @csrf
                    @method('delete')
                    <h2 class="text-lg font-medium text-gray-100 text-center">{{ __('Are you sure you want to delete your account?') }}</h2>
                    <p class="text-sm text-gray-400 text-center">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account') }}</p>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password" class="w-full p-3 bg-gray-700 border border-gray-600 rounded focus:ring-nutri-pink focus:border-nutri-pink text-sm sm:text-base" placeholder="{{ __('Password') }}">
                        @error('password', 'userDeletion')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-center gap-4">
                        <button x-on:click="$dispatch('close')" type="button" class="bg-gray-500 text-white p-2 rounded hover:bg-gray-600 text-sm sm:text-base">{{ __('Cancel') }}</button>
                        <button type="submit" class="bg-red-600 text-white p-2 rounded hover:bg-red-700 text-sm sm:text-base">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </x-modal>
        </section>
    </main>

    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        feather.replace();

        // Handle form visibility based on dropdown
        document.getElementById('form-selector').addEventListener('change', function () {
            const selectedForm = this.value;
            const forms = document.querySelectorAll('.form-section');

            forms.forEach(form => {
                if (selectedForm === 'all') {
                    form.style.display = 'block';
                } else {
                    form.style.display = form.id === `${selectedForm}-form` ? 'block' : 'none';
                }
            });
        });

        // Initialize form visibility to show profile form by default
        document.getElementById('form-selector').value = 'profile';
        document.getElementById('form-selector').dispatchEvent(new Event('change'));

        // SweetAlert for profile creation, update, and coupon application
        @if (session('status') === 'profile-created')
            Swal.fire({
                icon: 'success',
                title: '{{ __('Profile Created!') }}',
                text: '{{ __('Your NutriSnap profile has been successfully created') }}',
                timer: 3000,
                showConfirmButton: false,
            });
        @elseif (session('status') === 'profile-updated')
            Swal.fire({
                icon: 'success',
                title: '{{ __('Profile Updated!') }}',
                text: '{{ __('Your profile information has been successfully updated') }}',
                timer: 3000,
                showConfirmButton: false,
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: '{{ __('Error') }}',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false,
            });
        @endif
    </script>
@endsection