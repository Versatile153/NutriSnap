@extends('layouts.app')

@section('content')
    <!-- Hero Section with Image Slider -->
    <section id="home" class="py-24 parallax-bg bg-cover bg-center" style="background-image: url('https://via.placeholder.com/1920x1080?text=NutriSnap+Hero');">
        <div class="container mx-auto max-w-7xl px-4 flex flex-col md:flex-row items-center justify-between animate-slideUp">
            <div class="w-full md:w-1/2 text-center md:text-left">
                <h1 class="text-4xl sm:text-5xl font-bold mb-4 text-white">{{ __('Meet NutriSnap') }}</h1>
                <p class="text-xl sm:text-2xl mb-6 text-gray-200">{{ __('Track calories and nutrients with a single photo') }}</p>
                <p class="text-base sm:text-lg mb-6 text-gray-300">{{ __('Loved by 5M+ users with ⭐ 4.9 rating') }}</p>
                <a href="{{ route('register') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full text-base sm:text-lg font-semibold transition-colors">{{ __('Get Started Free') }}</a>
                
                @if (request()->query('status') === 'success')
                    <p class="text-green-400 text-sm mt-2">{{ urldecode(request()->query('message')) }}</p>
                @endif
                @if (request()->query('status') === 'error')
                    <p class="text-red-400 text-sm mt-2">{{ urldecode(request()->query('message')) }}</p>
                @endif
            </div>
            <div class="w-full md:w-1/2 mt-8 md:mt-0">
                <div x-data="{ currentSlide: 0, slides: ['https://www.calai.app/hero-image.webp', 'https://i.imgur.com/L8qERfd.png', 'https://plus.unsplash.com/premium_photo-1672862926934-d9f7e3f33632?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0'] }" x-init="setInterval(() => currentSlide = (currentSlide + 1) % slides.length, 5000)" class="relative">
                    <div class="overflow-hidden rounded-lg shadow-xl aspect-[3/2]">
                        <img x-show="currentSlide === 0" :src="slides[0]" alt="{{ __('NutriSnap App Preview') }}" class="w-full h-full object-cover transition-opacity duration-500" loading="lazy">
                        <img x-show="currentSlide === 1" :src="slides[1]" alt="{{ __('Food Analysis Preview') }}" class="w-full h-full object-cover transition-opacity duration-500" loading="lazy">
                        <img x-show="currentSlide === 2" :src="slides[2]" alt="{{ __('Fitness Tracking') }}" class="w-full h-full object-cover transition-opacity duration-500" loading="lazy">
                    </div>
                    <div class="flex justify-center mt-2 space-x-2">
                        <template x-for="index in slides.length" :key="index">
                            <button x-on:click="currentSlide = index - 1" :class="{ 'bg-pink-600': currentSlide === index - 1, 'bg-gray-400': currentSlide !== index - 1 }" class="w-3 h-3 rounded-full transition-colors"></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 sm:py-16 bg-gray-900">
        <div class="container mx-auto max-w-7xl px-4 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-8 sm:mb-12 text-white">{{ __('Why Choose NutriSnap?') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 sm:p-6 bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <img src="https://images.unsplash.com/photo-1669614660463-320f97121d4c?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0" alt="{{ __('Time Saving') }}" class="w-full h-32 sm:h-40 object-cover rounded-t-lg mb-4" loading="lazy">
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Save Time') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400">{{ __('Automatically calculates calories, protein, carbs, and fat. Add custom foods and recipes—no manual work needed.') }}</p>
                </div>
                <div class="p-4 sm:p-6 bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <img src="https://plus.unsplash.com/premium_photo-1664910616659-40d868fb059c?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0" alt="{{ __('Fitness Integration') }}" class="w-full h-32 sm:h-40 object-cover rounded-t-lg mb-4" loading="lazy">
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Fitness Sync') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400">{{ __('Integrates with your favorite fitness apps to track nutrition and exercise seamlessly.') }}</p>
                </div>
                <div class="p-4 sm:p-6 bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <img src="https://images.unsplash.com/photo-1642267209558-d32315e077f8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0" alt="{{ __('Health Goals') }}" class="w-full h-32 sm:h-40 object-cover rounded-t-lg mb-4" loading="lazy">
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Health Goals') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400">{{ __('Snap a photo for AI-driven nutrient analysis, tailored to your diet goals or conditions like diabetes or hypertension.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- New Feature: Health Condition Customization -->
    <section id="health-customization" class="py-12 sm:py-16 bg-gray-800">
        <div class="container mx-auto max-w-7xl px-4 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6 sm:mb-8 text-white">{{ __('Personalized Nutrition Tracking') }}</h2>
            <p class="text-xl sm:text-2xl mb-4 sm:mb-6 text-pink-400">{{ __('Tailored for Your Health Needs') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 sm:p-6 bg-gray-700 rounded-lg shadow-lg">
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Diabetes Support') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400">{{ __('Upload a photo, and NutriSnap recommends foods to avoid and ideal portion sizes for blood sugar control.') }}</p>
                </div>
                <div class="p-4 sm:p-6 bg-gray-700 rounded-lg shadow-lg">
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Hypertension Management') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400">{{ __('Get sodium intake highlights with every photo analysis to support heart-healthy eating.') }}</p>
                </div>
                <div class="p-4 sm:p-6 bg-gray-700 rounded-lg shadow-lg">
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Custom Diet Goals') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400">{{ __('Input your weight and height goals; NutriSnap suggests portion adjustments to eat more, less, or avoid specific foods.') }}</p>
                </div>
            </div>
            <div class="mt-8">
                <a href="{{ route('register') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-full text-base font-semibold transition-colors">{{ __('Try Personalized Tracking') }}</a>
            </div>
        </div>
    </section>

    <!-- User Dashboard Preview -->
    <section id="dashboard" class="py-12 sm:py-16 bg-gray-900">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex flex-col md:flex-row items-center md:items-start md:space-x-10">
                <!-- Left: Image -->
                <div class="flex-shrink-0 w-full md:w-1/2 flex justify-center md:justify-start mb-8 md:mb-0">
                    <img src="https://i.imgur.com/pCxhSkp.png" 
                         alt="{{ __('NutriSnap Dashboard') }}" 
                         class="w-full max-w-xs sm:max-w-md rounded-lg shadow-xl" 
                         loading="lazy">
                </div>
                <!-- Right: Text -->
                <div class="w-full md:w-1/2 text-center md:text-left">
                    <h2 class="text-3xl sm:text-4xl font-bold mb-6 text-white">{{ __('Your NutriSnap Dashboard') }}</h2>
                    <p class="text-base sm:text-lg mb-6 text-gray-300">{{ __('Track your meals, view analytics, and share results effortlessly.') }}</p>
                    <p class="text-sm sm:text-base text-gray-400 mb-6">{{ __('Upload photos, request corrections, or share on social media to earn free coupons!') }}</p>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-block bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-full text-base font-semibold transition-colors">{{ __('Explore Dashboard') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Seller Integration -->
    <section id="sellers" class="py-12 sm:py-16 bg-gray-800">
        <div class="container mx-auto max-w-7xl px-4 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-8 sm:mb-12 text-white">{{ __('For Food Sellers') }}</h2>
            <p class="text-base sm:text-lg mb-6 text-gray-300">{{ __('Enhance your site with NutriSnap’s widget or API for real-time calorie and nutrient analysis.') }}</p>
            <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-12">
                <img src="https://www.calai.app/analyzed.png" alt="{{ __('Widget Preview') }}" class="w-full sm:w-1/2 max-w-xs sm:max-w-md rounded-lg shadow-xl" loading="lazy">
                <div class="w-full sm:w-1/2 text-left">
                    <ul class="text-sm sm:text-base text-gray-400 list-disc list-inside space-y-2">
                        <li>{{ __('Embed our JS widget for automatic photo analysis.') }}</li>
                        <li>{{ __('Use our REST API for seamless integration.') }}</li>
                        <li>{{ __('Display calorie and nutrient info on product pages.') }}</li>
                        <li>{{ __('Partner with us for revenue-sharing opportunities.') }}</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="mt-6 inline-block bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-full text-base font-semibold transition-colors">{{ __('Become a Partner') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Widget Promotion Section -->
    <section id="widget-promotion" class="py-12 sm:py-16 bg-gray-900">
        <div class="container mx-auto max-w-7xl px-4 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-8 sm:mb-12 text-white">{{ __('Embed NutriSnap on Your Website') }}</h2>
            <p class="text-base sm:text-lg mb-6 text-gray-300">{{ __('Bring AI-powered food analysis to your users with the NutriSnap Widget. Perfect for food blogs, e-commerce, and health platforms.') }}</p>
            <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-12">
                <img src="https://i.imgur.com/qnzoG8r.png" alt="{{ __('NutriSnap Widget Demo') }}" class="w-full sm:w-1/2 max-w-xs sm:max-w-md rounded-lg shadow-xl" loading="lazy">
                <div class="w-full sm:w-1/2 text-left">
                    <ul class="text-sm sm:text-base text-gray-400 list-disc list-inside space-y-2">
                        <li>{{ __('Quickly embed with a single script tag.') }}</li>
                        <li>{{ __('Customizable UI to match your site’s design.') }}</li>
                        <li>{{ __('Secure API access with token-based authentication.') }}</li>
                        <li>{{ __('Real-time nutrient analysis for your users.') }}</li>
                    </ul>
                    <div class="mt-6 flex flex-col sm:flex-row gap-4">
                        <a href="/docs/nutrisnap-widget" class="inline-block bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-full text-base font-semibold transition-colors">{{ __('View Embedding Guide') }}</a>
                        <a href="/api-docs" class="inline-block bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-full text-base font-semibold transition-colors">{{ __('API Documentation') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-12 sm:py-16 bg-gray-800">
        <div class="container mx-auto max-w-7xl px-4 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-8 sm:mb-12 text-white">{{ __('Pricing Plans') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 sm:p-6 bg-gray-700 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <svg class="w-full h-32 sm:h-40 text-pink-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Free') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400 mb-4">{{ __('Basic photo analysis and calorie tracking.') }}</p>
                    <p class="text-xl sm:text-2xl text-pink-400 font-bold">{{ __('$0') }}</p>
                    <a href="{{ route('register') }}" class="mt-4 inline-block bg-pink-600 hover:bg-pink-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full text-sm sm:text-base">{{ __('Get Started') }}</a>
                </div>
                <div class="p-4 sm:p-6 bg-gray-700 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <svg class="w-full h-32 sm:h-40 text-pink-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Premium') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400 mb-4">{{ __('Unlimited tracking, fitness integration, health condition support, and dark mode.') }}</p>
                    <p class="text-xl sm:text-2xl text-pink-400 font-bold">{{ __('$9.99/mo') }}</p>
                    <a href="{{ route('register') }}" class="mt-4 inline-block bg-pink-600 hover:bg-pink-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full text-sm sm:text-base">{{ __('Subscribe') }}</a>
                </div>
                <div class="p-4 sm:p-6 bg-gray-700 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <svg class="w-full h-32 sm:h-40 text-pink-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm0 2c-2.21 0-4 1.79-4 4v7h8v-7c0-2.21-1.79-4-4-4zm0 2c1.1 0 2 .9 2 2v5h-4v-5c0-1.1.9-2 2-2z"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-white">{{ __('Pro') }}</h3>
                    <p class="text-sm sm:text-base text-gray-400 mb-4">{{ __('All Premium features, advanced analytics, and priority correction requests.') }}</p>
                    <p class="text-xl sm:text-2xl text-pink-400 font-bold">{{ __('$19.99/mo') }}</p>
                    <a href="{{ route('register') }}" class="mt-4 inline-block bg-pink-600 hover:bg-pink-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full text-sm sm:text-base">{{ __('Subscribe') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-12 sm:py-16 bg-gray-900">
        <div class="container mx-auto max-w-7xl px-4 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-8 sm:mb-12 text-white">{{ __('What Our Users Say') }}</h2>
            <div x-data="{ currentIndex: 0, testimonials: [
                { name: 'Aryan Thakur', quote: '{{ __('Lost 17 lbs with NutriSnap’s easy photo tracking—super reliable!') }}', img: 'https://plus.unsplash.com/premium_photo-1661690765823-168ad97adf14?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0' },
                { name: 'Ordinary Tony', quote: '{{ __('Bulking for a year with NutriSnap—game-changer for my gains! 👏') }}', img: 'https://images.unsplash.com/photo-1556911073-a517e752729c?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0' },
                { name: 'Parth Israni', quote: '{{ __('Tracks my meals without stress and looks amazing! 💓') }}', img: 'https://media.istockphoto.com/id/2149951178/photo/portrait-of-caucasian-female-doctor-working-at-the-ordination.webp?a=1&b=1&s=612x612&w=0&k=20&c=xnbCJEM_iI3MzyZJLTxDdeWptHM2oEX4sxa4Rmy_GtE=' },
                { name: 'Mathias', quote: '{{ __('Started using it yesterday—already 5⭐️ for simplicity!') }}', img: 'https://via.placeholder.com/100x100?text=Mathias' },
                { name: 'Ms Nsofor', quote: '{{ __('Perfect for calorie control, especially for my diet needs! 🙌🔥') }}', img: 'https://via.placeholder.com/100x100?text=Ms+Nsofor' }
            ] }" x-init="setInterval(() => currentIndex = (currentIndex + 1) % testimonials.length, 5000)" class="relative">
                <div class="overflow-hidden rounded-lg">
                    <template x-for="testimonial in testimonials" :key="testimonial.name">
                        <div x-show="currentIndex === testimonials.indexOf(testimonial)" class="p-4 sm:p-6 bg-gray-800 rounded-lg shadow-lg transition-opacity duration-500">
                            <img :src="testimonial.img" :alt="testimonial.name" class="w-12 sm:w-16 h-12 sm:h-16 rounded-full mx-auto mb-4" loading="lazy">
                            <p class="text-sm sm:text-base text-gray-300 italic mb-4" x-text="testimonial.quote"></p>
                            <p class="text-pink-400 font-semibold text-sm sm:text-base" x-text="testimonial.name"></p>
                        </div>
                    </template>
                </div>
                <div class="flex justify-center mt-4 space-x-2">
                    <template x-for="index in testimonials.length" :key="index">
                        <button x-on:click="currentIndex = index - 1" :class="{ 'bg-pink-600': currentIndex === index - 1, 'bg-gray-400': currentIndex !== index - 1 }" class="w-3 h-3 rounded-full transition-colors"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <!-- Ratings Section -->
    <section id="ratings" class="py-12 sm:py-16 bg-gray-800">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex flex-col-reverse lg:flex-row items-center justify-between gap-10">
                <div class="flex-1 text-center lg:text-left">
                    <h2 class="text-3xl sm:text-4xl font-bold mb-6 sm:mb-8 text-white">{{ __('Over 100K 5-Star Ratings') }}</h2>
                    <div class="flex flex-col sm:flex-row items-center lg:items-start justify-center lg:justify-start gap-8">
                        <div class="text-center lg:text-left">
                            <p class="text-xl sm:text-2xl text-white">{{ __('4.8/5') }}</p>
                            <span class="text-3xl sm:text-4xl text-yellow-400">⭐⭐⭐⭐⭐</span>
                            <div class="w-48 bg-gray-700 rounded-full h-2.5 mt-2 mx-auto lg:mx-0">
                                <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 96%"></div>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">{{ __('Web Platform') }}</p>
                        </div>
                        <div class="text-center lg:text-left">
                            <p class="text-xl sm:text-2xl text-white">{{ __('4.7/5') }}</p>
                            <span class="text-3xl sm:text-4xl text-yellow-400">⭐⭐⭐⭐⭐</span>
                            <div class="w-48 bg-gray-700 rounded-full h-2.5 mt-2 mx-auto lg:mx-0">
                                <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 94%"></div>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">{{ __('User Reviews') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 flex justify-center">
                    <img src="https://i.imgur.com/H0BYS9h.png" alt="{{ __('Laurel Wreath') }}" class="max-w-xs sm:max-w-sm lg:max-w-md" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-12 sm:py-16 bg-gray-900">
        <div class="container mx-auto max-w-7xl px-4 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-8 sm:mb-12 text-white">{{ __('Get in Touch') }}</h2>
            <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-12">
                <img src="https://i.imgur.com/ExGmo9q.png" alt="{{ __('Contact NutriSnap') }}" class="w-full sm:w-1/2 max-w-xs sm:max-w-md rounded-lg shadow-xl" loading="lazy">
                <form action="{{ route('contact.submit') }}" method="POST" class="w-full sm:w-1/2 space-y-4">
                    @csrf
                    <div>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Your Name') }}" class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-pink-400 @error('name') border-red-500 @enderror" required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Your Email') }}" class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-pink-400 @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <textarea name="message" placeholder="{{ __('Your Message') }}" class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-pink-400 h-24 sm:h-32 @error('message') border-red-500 @enderror" required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full sm:w-auto bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 sm:py-3 rounded-full transition-colors">{{ __('Send Message') }}</button>
                    <!-- Fallback inline messages -->
                    @if (request()->query('status') === 'success')
                        <p class="text-green-400 text-sm mt-2">{{ urldecode(request()->query('message')) }}</p>
                    @endif
                    @if (request()->query('status') === 'error')
                        <p class="text-red-400 text-sm mt-2">{{ urldecode(request()->query('message')) }}</p>
                    @endif
                </form>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                console.log('Checking for query parameters');
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('status');
                const message = urlParams.get('message');

                if (status && message) {
                    console.log('Status:', status, 'Message:', decodeURIComponent(message));
                    alert(decodeURIComponent(message));
                    // Clear query parameters from URL
                    window.history.replaceState({}, document.title, window.location.pathname);
                } else {
                    console.log('No status or message found in query parameters');
                }
            });
        </script>
    @endpush
@endsection