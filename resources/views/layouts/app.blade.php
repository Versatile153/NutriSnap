<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('NutriSnap - AI Calorie Tracker') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Serif+KR:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'Noto Serif KR', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        border: '#4B5563'
                    }
                }
            }
        };
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Feather icons -->
    <script src="https://unpkg.com/feather-icons" defer></script>
</head>
<body class="svg-bg relative" x-data="{ mobileMenuOpen: false, selectedLanguage: '{{ app()->getLocale() }}' }">
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="spinner"></div>
        <div class="logo">{{ __('NutriSnap') }}</div>
    </div>

    <!-- Watermark -->
    <div class="watermark">{{ __('NutriSnap AI') }}</div>

    <!-- Navigation -->
    <header class="bg-gray-900/95 backdrop-blur-sm p-4 shadow-xl sticky top-0 z-50 animate-fadeIn container mx-auto max-w-7xl px-4 flex flex-col md:flex-row items-center justify-between animate-slideUp">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-3xl font-bold text-pink-400 tracking-tight">{{ __('NutriSnap') }}</a>
            <nav class="hidden md:flex space-x-8 items-center">
                <a href="/" class="nav-link text-lg font-medium">{{ __('Home') }}</a>
                <a href="#features" class="nav-link text-lg font-medium">{{ __('Features') }}</a>
                <a href="#testimonials" class="nav-link text-lg font-medium">{{ __('Testimonials') }}</a>
                <a href="/docs/nutrisnap-widget" class="nav-link text-lg font-medium">{{ __('Docs') }}</a>
                @auth
                    <div class="relative group">
                        <button class="nav-link text-lg font-medium">{{ ucfirst(auth()->user()->name) }}</button>
                        <div class="absolute hidden group-hover:block bg-gray-800/95 backdrop-blur-sm p-4 rounded-xl shadow-2xl mt-3 space-y-3 animate-menuSlide">
                            <a href="{{ route('dashboard') }}" class="block hover:text-pink-400 p-2 rounded-lg transition-colors text-base">{{ __('Dashboard') }}</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block hover:text-pink-400 p-2 rounded-lg transition-colors text-base">{{ __('Admin') }}</a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left hover:text-pink-400 p-2 rounded-lg transition-colors text-base">{{ __('Logout') }}</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-link text-lg font-medium">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="nav-link text-lg font-medium">{{ __('Register') }}</a>
                @endauth
                <!-- Language Dropdown (Desktop) -->
                <div class="language-dropdown" x-data="{ open: false }">
                    <select x-model="selectedLanguage" @change="changeLanguage($event)" class="text-sm bg-gray-800 text-white rounded-lg p-2">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                        <option value="ko" {{ app()->getLocale() == 'ko' ? 'selected' : '' }}>{{ __('Korean') }}</option>
                        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>{{ __('Spanish') }}</option>
                    </select>
                </div>
            </nav>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-pink-400 focus:outline-none p-3 rounded-lg transition-transform hover:bg-gray-800/50" aria-label="{{ __('Toggle Menu') }}">
                <svg class="w-7 h-7" :class="{ 'rotate-90': mobileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="md:hidden bg-gray-800/95 backdrop-blur-sm p-6 mt-3 rounded-xl shadow-2xl absolute w-full top-full left-0 z-40">
            <nav class="flex flex-col space-y-4">
                <a href="/" class="hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Home') }}</a>
                <a href="#features" class="hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Features') }}</a>
                <a href="#testimonials" class="hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Testimonials') }}</a>
                <a href="/docs/nutrisnap-widget" class="hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Docs') }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Dashboard') }}</a>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Admin') }}</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="hover:text-pink-400 transition-colors text-lg font-medium py-3 px-4 rounded-lg hover:bg-gray-700/50">{{ __('Register') }}</a>
                @endauth
                <!-- Language Dropdown (Mobile) -->
                <div class="language-dropdown">
                    <select x-model="selectedLanguage" @change="changeLanguage($event)" class="w-full text-sm bg-gray-800 text-white rounded-lg p-2">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                        <option value="ko" {{ app()->getLocale() == 'ko' ? 'selected' : '' }}>{{ __('Korean') }}</option>
                        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>{{ __('Spanish') }}</option>
                    </select>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto mt-10 animate-slideUp">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/95 backdrop-blur-sm py-8 sm:py-10 md:py-12 mt-16 shadow-inner">
        <div class="container mx-auto grid grid-cols-2 lg:grid-cols-4 gap-8 px-4 sm:px-6 lg:px-8 text-center sm:text-left">
            <!-- NutriSnap Branding -->
            <div class="col-span-1">
                <h3 class="text-2xl font-bold mb-4 text-pink-400 tracking-tight">{{ __('NutriSnap') }}</h3>
                <p class="text-sm leading-relaxed text-gray-300">{{ __('AI-powered calorie tracking for a healthier you. Snap, analyze, succeed.') }}</p>
            </div>
            <!-- Quick Links -->
            <div class="col-span-1">
                <h3 class="text-xl font-semibold mb-4 text-white">{{ __('Quick Links') }}</h3>
                <ul class="space-y-3">
                    <li><a href="#features" class="footer-link text-sm">{{ __('Features') }}</a></li>
                    <li><a href="#testimonials" class="footer-link text-sm">{{ __('Testimonials') }}</a></li>
                    <li><a href="#pricing" class="footer-link text-sm">{{ __('Pricing') }}</a></li>
                    <li><a href="#contact" class="footer-link text-sm">{{ __('Contact') }}</a></li>
                    <li><a href="/privacy" class="footer-link text-sm">{{ __('Privacy') }}</a></li>
                    <li><a href="/terms" class="footer-link text-sm">{{ __('Terms & Conditions') }}</a></li>
                    <li><a href="/docs/nutrisnap-widget" class="footer-link text-sm">{{ __('Docs') }}</a></li>
                    <!-- Language Dropdown (Footer) -->
                    <li class="language-dropdown mt-4">
                        <select x-model="selectedLanguage" @change="changeLanguage($event)" class="text-sm bg-gray-800 text-white rounded-lg p-2">
                            <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                            <option value="ko" {{ app()->getLocale() == 'ko' ? 'selected' : '' }}>{{ __('Korean') }}</option>
                            <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>{{ __('Spanish') }}</option>
                        </select>
                    </li>
                </ul>
            </div>
            <!-- Social Links -->
            <div class="col-span-1">
                <h3 class="text-xl font-semibold mb-4 text-white">{{ __('Social') }}</h3>
                <div class="flex justify-center sm:justify-start space-x-6">
                    <a href="#" aria-label="Twitter" class="text-pink-400 hover:text-pink-600 transition-transform transform hover:scale-110">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="text-pink-400 hover:text-pink-600 transition-transform transform hover:scale-110">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5a4.25 4.25 0 00-4.25 4.25v8.5a4.25 4.25 0 004.25 4.25h8.5a4.25 4.25 0 004.25-4.25v-8.5a4.25 4.25 0 00-4.25-4.25h-8.5zm8.75 2a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5zm-3.75 1.25a5 5 0 110 10 5 5 0 010-10zm0 1.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z"/></svg>
                    </a>
                    <a href="#" aria-label="Facebook" class="text-pink-400 hover:text-pink-600 transition-transform transform hover:scale-110">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.597 0 0 .597 0 1.325v21.351C0 23.403.597 24 1.325 24h11.495v-9.294H9.691v-3.622h3.129V7.41c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.794.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.311h3.59l-.467 3.622h-3.123V24h6.116c.728 0 1.325-.597 1.325-1.324V1.325C24 .597 23.403 0 22.675 0z"/></svg>
                    </a>
                </div>
            </div>
            <!-- Subscribe Form -->
            <div class="col-span-1">
                <h3 class="text-xl font-semibold mb-4 text-white">{{ __('Stay Updated') }}</h3>
                <form class="flex flex-col sm:flex-row gap-3">
                    <input type="email" placeholder="{{ __('Your email') }}" class="p-3 rounded-lg bg-gray-800 border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-pink-400 transition-shadow shadow-sm hover:shadow-lg w-full">
                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white p-3 rounded-lg transition-shadow shadow-sm hover:shadow-lg w-full sm:w-auto">{{ __('Subscribe') }}</button>
                </form>
            </div>
        </div>
        <div class="mt-8 text-center text-sm text-gray-400">
            &copy; 2025 {{ __('NutriSnap') }}. {{ __('All rights reserved.') }}
        </div>
    </footer>

    <!-- Modified FAB -->
    <div class="fixed bottom-10 right-10 group" x-data="{ fabTooltip: false }">
        <a href="{{ route('register') }}" class="bg-pink-600 text-white p-5 rounded-full shadow-xl hover:bg-pink-700 focus:outline-none animate-pulse" @mouseenter="fabTooltip = true" @mouseleave="fabTooltip = false" @touchstart="fabTooltip = true" @touchend="fabTooltip = false" aria-label="{{ __('Get Started') }}">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </a>
        <span x-show="fabTooltip" class="absolute bottom-16 right-0 bg-gray-800/95 backdrop-blur-sm text-white p-3 rounded-lg shadow-xl opacity-0 transition-opacity duration-300 group-hover:opacity-100" style="min-width: 140px; text-align: center;">{{ __('Start Your Journey') }}</span>
    </div>

    <!-- Back-to-Top Button -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="fixed bottom-28 right-10 bg-gray-700/80 backdrop-blur-sm text-white p-4 rounded-full shadow-xl hover:bg-gray-600 focus:outline-none opacity-0 transition-opacity duration-300" :class="{ 'opacity-100': window.scrollY > 100 }" aria-label="{{ __('Back to Top') }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <script>
        // Preloader Script
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            setTimeout(() => {
                preloader.classList.add('hidden');
            }, 1000);
        });

        // Initialize Feather icons
        document.addEventListener('DOMContentLoaded', () => {
            if (window.feather) {
                feather.replace();
            }
        });

        // Language Change Handler
        function changeLanguage(event) {
            const lang = event.target.value;
            fetch('/language/' + lang, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ lang: lang })
            }).then(response => {
                if (response.ok) {
                    localStorage.setItem('language', lang); // Store selected language
                    window.location.reload();
                } else {
                    console.error('Language switch failed:', response.statusText);
                }
            }).catch(error => {
                console.error('Error during language switch:', error);
            });
        }

        // Geolocation-based Language Detection
        document.addEventListener('DOMContentLoaded', () => {
            // Only run if no language is set in session or localStorage
            if (!localStorage.getItem('language') && !'{{ Session::get('locale') }}') {
                fetch('https://ipapi.co/json/')
                    .then(response => response.json())
                    .then(data => {
                        const country = data.country_code;
                        let lang = 'en'; // Default language
                        if (country === 'KR') lang = 'ko';
                        else if (country === 'ES' || country === 'MX' || country === 'AR') lang = 'es';

                        fetch('/language/' + lang, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ lang: lang })
                        }).then(response => {
                            if (response.ok) {
                                localStorage.setItem('language', lang);
                                window.location.reload();
                            } else {
                                console.error('Geolocation language switch failed:', response.statusText);
                            }
                        }).catch(error => {
                            console.error('Geolocation language detection failed:', error);
                        });
                    });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>