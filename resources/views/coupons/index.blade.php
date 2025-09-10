@extends('layouts.app1')

@section('content')
    <!-- Assume layouts.app1 includes the watermark and header similar to previous templates -->
    <div class="watermark">{{ __('NutriSnap AI') }}</div>

    <!-- Header (assumed from layouts.app1, similar to previous templates) -->
    <header class="bg-gray-800/95 backdrop-blur-sm sticky top-0 z-40 border-b border-gray-800 shadow-md">
        <div class="mx-auto max-w-[1400px] px-4 sm:px-6">
            <div class="h-16 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-nutri-pink">{{ __('NutriSnap') }}</a>
                <div class="flex items-center gap-4">
                    @auth
                        <div class="relative group">
                            <button class="nav-link text-lg font-medium text-gray-300 hover:text-nutri-pink">{{ ucfirst(auth()->user()->name) }}</button>
                            <div class="absolute hidden group-hover:block bg-gray-800/95 backdrop-blur-sm p-4 rounded-xl shadow-2xl mt-3 space-y-3 animate-menuSlide right-0">
                                <a href="/settings" class="block hover:text-nutri-pink p-2 rounded-lg transition-colors text-base">{{ __('Profile') }}</a>
                                <a href="{{ route('settings') }}" class="block hover:text-nutri-pink p-2 rounded-lg transition-colors text-base">{{ __('Settings') }}</a>
                                <form action="{{ route('logout') }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left hover:text-nutri-pink p-2 rounded-lg transition-colors text-base">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 mt-10 pb-24">
        <h2 class="text-2xl md:text-3xl font-bold mb-8 text-white tracking-tight">{{ __('Your Coupons') }}</h2>

        <!-- Active Coupons Section -->
        <section class="bg-gray-900 rounded-xl p-6 sm:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl md:text-2xl font-semibold text-white">{{ __('Active Coupons') }}</h3>
                <a href="{{ route('dashboard') }}" class="text-sm md:text-base text-pink-400 hover:text-pink-300 transition-colors">{{ __('Back to Dashboard') }}</a>
            </div>
            <p class="text-sm text-gray-300 mb-6">{{ __('Your available coupons for subscription discounts') }}</p>

            @if ($activeCoupons->isEmpty())
                <p class="text-sm text-gray-400 italic">{{ __('No active coupons available. Share a meal analysis on social media to earn one!') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($activeCoupons as $coupon)
                        <div class="bg-gray-800 rounded-lg p-5 flex flex-col justify-between hover:bg-gray-700 transition-colors duration-200">
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide">{{ __('Coupon Code') }}</p>
                                <p class="text-lg font-bold text-pink-400 truncate">{{ $coupon->code }}</p>
                                <p class="text-sm text-gray-300 mt-2">{{ __('Discount') }}: <span class="font-semibold">{{ $coupon->discount_percentage }}% off</span></p>
                                <p class="text-sm text-gray-300">{{ __('Expires') }}: <span class="font-semibold">{{ $coupon->expires_at->format('M d, Y') }}</span></p>
                            </div>
                            <form action="{{ route('coupons.apply') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="code" value="{{ $coupon->code }}">
                                <button type="submit" class="w-full bg-pink-500 text-white text-sm font-medium py-2 rounded-lg hover:bg-pink-600 transition-colors">{{ __('Apply to Subscription') }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Share a Meal Section -->
        <section class="mt-8 bg-gray-900 rounded-xl p-6 sm:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl md:text-2xl font-semibold text-white mb-4">{{ __('Share a Meal to Earn a Coupon') }}</h3>
            <p class="text-sm text-gray-300 mb-6">{{ __('Share a recent meal analysis on social media and submit the link for admin approval to earn a coupon!') }}</p>
            <form action="{{ route('coupons.share') }}" method="POST" class="max-w-lg">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="meal_id" class="text-sm text-gray-300 font-medium">{{ __('Select Meal') }}</label>
                        <select name="meal_id" id="meal_id" class="mt-1 w-full bg-gray-700 text-white rounded-lg p-3 focus:ring-2 focus:ring-pink-400 focus:outline-none">
                            @foreach (auth()->user()->meals as $meal)
                                <option value="{{ $meal->id }}">{{ __('Meal') }} #{{ $meal->id }} ({{ $meal->meal_type }} - {{ $meal->created_at->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                        @error('meal_id')
                            <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="platform" class="text-sm text-gray-300 font-medium">{{ __('Social Media Platform') }}</label>
                        <select name="platform" id="platform" class="mt-1 w-full bg-gray-700 text-white rounded-lg p-3 focus:ring-2 focus:ring-pink-400 focus:outline-none">
                            <option value="twitter">{{ __('Twitter/X') }}</option>
                            <option value="facebook">{{ __('Facebook') }}</option>
                            <option value="instagram">{{ __('Instagram') }}</option>
                        </select>
                        @error('platform')
                            <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="share_link" class="text-sm text-gray-300 font-medium">{{ __('Share Link') }}</label>
                        <input type="url" name="share_link" id="share_link" class="mt-1 w-full bg-gray-700 text-white rounded-lg p-3 focus:ring-2 focus:ring-pink-400 focus:outline-none" placeholder="{{ __('Enter share link (e.g., https://x.com/post/123)') }}" required>
                        @error('share_link')
                            <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="w-full bg-pink-500 text-white text-sm font-medium py-3 rounded-lg hover:bg-pink-600 transition-colors">{{ __('Submit Share for Approval') }}</button>
                </div>
            </form>
        </section>

        <!-- Share Submissions Section -->
        <section class="mt-8 bg-gray-900 rounded-xl p-6 sm:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-xl md:text-2xl font-semibold text-white mb-4">{{ __('Your Share Submissions') }}</h3>
            <p class="text-sm text-gray-300 mb-6">{{ __('Track the status of your social media share submissions') }}</p>
            @if ($shareSubmissions->isEmpty())
                <p class="text-sm text-gray-400 italic">{{ __('No share submissions yet. Submit a share above to earn a coupon!') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($shareSubmissions as $meal)
                        <div class="bg-gray-800 rounded-lg p-5 flex flex-col justify-between hover:bg-gray-700 transition-colors duration-200">
                            <div>
                                <p class="text-sm text-gray-300">{{ __('Meal') }} #{{ $meal->id }} ({{ $meal->meal_type }})</p>
                                <p class="text-sm text-gray-300">{{ __('Platform') }}: <span class="font-semibold">{{ ucfirst($meal->platform ?? ($meal->share_proof['platform'] ?? __('Unknown'))) }}</span></p>
                                <p class="text-sm text-gray-300">
                                    {{ __('Share Link') }}: 
                                    <a href="{{ $meal->share_link['url'] ?? $meal->share_proof['url'] ?? '#' }}" 
                                       class="text-pink-400 hover:text-pink-300 underline" 
                                       target="_blank">
                                        {{ __('View Post') }}
                                    </a>
                                </p>
                                <p class="text-sm text-gray-300">{{ __('Status') }}: <span class="font-semibold {{ $meal->share_link['approved'] ?? $meal->share_proof['approved'] ?? false ? 'text-green-400' : 'text-yellow-400' }}">{{ $meal->share_link['approved'] ?? $meal->share_proof['approved'] ?? false ? __('Approved') : __('Pending') }}</span></p>
                                <p class="text-sm text-gray-300">{{ __('Submitted') }}: <span class="font-semibold">{{ $meal->updated_at->format('M d, Y') }}</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.1/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const status = '{{ session('status') }}';
            const error = '{{ session('error') }}';
            
            if (status === 'share-submitted') {
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('Share Submitted!') }}',
                    text: '{{ __('Your share has been submitted for admin approval.') }}',
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#ec4899',
                    timer: 3000,
                    showConfirmButton: false,
                });
            } else if (status === 'coupon-applied') {
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('Coupon Applied!') }}',
                    text: '{{ __('Your coupon has been applied. Proceed to settings to update your subscription.') }}',
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#ec4899',
                    timer: 3000,
                    showConfirmButton: false,
                });
            } else if (error) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Error') }}',
                    text: error,
                    background: '#1f2937',
                    color: '#f3f4f6',
                    confirmButtonColor: '#ec4899',
                    timer: 3000,
                    showConfirmButton: false,
                });
            }
        });
    </script>
@endsection