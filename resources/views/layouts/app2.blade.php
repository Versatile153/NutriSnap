<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('NutriSnap') }} - @yield('title', __('Admin Dashboard'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Serif+KR:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'Noto Serif KR', 'ui-sans-serif', 'system-ui'] 
                    },
                    colors: { 
                        'nutri-blue': '#3B82F6',
                        'nutri-dark': '#1F2937',
                        'nutri-light': '#F3F4F6'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-in-out',
                        'hamburger': 'hamburger 0.3s ease-in-out'
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                        hamburger: {
                            '0%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(90deg)' }
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/feather-icons@4.28.0/dist/feather.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.1/dist/sweetalert2.min.js" defer></script>
    <style>
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.15;
            font-size: 5rem;
            font-weight: 800;
            color: #3B82F6;
            transform: rotate(-30deg);
            pointer-events: none;
            z-index: 10;
            animation: pulse 3s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1) rotate(-30deg); }
            50% { transform: scale(1.05) rotate(-30deg); }
        }
        [x-cloak] { display: none; }
        .nav-item:hover .nav-tooltip {
            opacity: 1;
        }
        .nav-tooltip {
            position: absolute;
            top: 100%;
            margin-top: 4px;
            background: #1F2937;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 50;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        .hamburger {
            width: 24px;
            height: 16px;
            position: relative;
            cursor: pointer;
        }
        .hamburger span {
            display: block;
            position: absolute;
            height: 3px;
            width: 100%;
            background: #3B82F6;
            border-radius: 3px;
            opacity: 1;
            left: 0;
            transition: all 0.3s ease-in-out;
        }
        .hamburger span:nth-child(1) { top: 0; }
        .hamburger span:nth-child(2) { top: 6px; }
        .hamburger span:nth-child(3) { top: 12px; }
        .hamburger.open span:nth-child(1) {
            transform: translateY(6px) rotate(45deg);
        }
        .hamburger.open span:nth-child(2) {
            opacity: 0;
        }
        .hamburger.open span:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg);
        }
    </style>
</head>
<body class="bg-nutri-light dark:bg-nutri-dark text-gray-900 dark:text-gray-100 font-sans min-h-screen" x-data="{
    navOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    selectedLanguage: '{{ app()->getLocale() }}'
}" x-init="darkMode && document.documentElement.classList.add('dark'); if (!window.Alpine) console.error('Alpine.js failed to load');" x-cloak>
    <!-- Preloader -->
    <div class="preloader fixed inset-0 bg-nutri-light dark:bg-nutri-dark flex items-center justify-center z-50" id="preloader" x-show="true" x-transition:leave="transition duration-300">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-nutri-blue"></div>
        <div class="ml-4 text-2xl font-bold text-nutri-blue">{{ __('NutriSnap') }}</div>
    </div>

    <!-- Watermark -->
    <div class="watermark">{{ __('NutriSnap AI') }}</div>

    <!-- Header with Top Navigation -->
    <header class="bg-nutri-dark dark:bg-gray-800/95 backdrop-blur-sm sticky top-0 z-40 border-b border-gray-700 shadow-md">
        <div class="mx-auto max-w-[1400px] px-4 sm:px-6">
            <div class="h-16 flex items-center justify-between">
                <!-- Left: Logo and Hamburger -->
                <div class="flex items-center gap-3">
                    <button class="lg:hidden" @click="navOpen = !navOpen; $refs.hamburger.classList.toggle('open')" :aria-label="navOpen ? '{{ __('Close Menu') }}' : '{{ __('Open Menu') }}'">
                        <div class="hamburger" x-ref="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>
                    <span class="text-lg font-bold text-nutri-blue">{{ __('NutriSnap') }}</span>
                </div>

                <!-- Center: Navigation (Desktop) -->
                <nav class="hidden lg:flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item relative flex items-center gap-2 text-nutri-blue px-3 py-2 rounded-md font-semibold">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                    <a href="{{ route('admin.emails') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="mail" class="w-5 h-5"></i>
                        <span>{{ __('Emails') }}</span>
                    </a>
                    <a href="{{ route('admin.analysis') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                        <span>{{ __('Analysis') }}</span>
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="tag" class="w-5 h-5"></i>
                        <span>{{ __('Coupons') }}</span>
                    </a>
                    <a href="{{ route('admin.coupons.create') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="plus-circle" class="w-5 h-5"></i>
                        <span>{{ __('Create Coupon') }}</span>
                    </a>
                    <a href="{{ route('admin.shares.index') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="share-2" class="w-5 h-5"></i>
                        <span>{{ __('Shares') }}</span>
                    </a>
                    <a href="{{ route('admin.foods.index') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="apple" class="w-5 h-5"></i>
                        <span>{{ __('Foods') }}</span>
                    </a>
                </nav>

                <!-- Right: Profile, Dark Mode, Language -->
                <div class="flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark')" class="p-2 rounded-md hover:bg-gray-700" aria-label="{{ __('Toggle Dark Mode') }}">
                        <i data-feather="moon" class="w-5 h-5 text-gray-300" x-show="!darkMode"></i>
                        <i data-feather="sun" class="w-5 h-5 text-gray-300" x-show="darkMode"></i>
                    </button>
                    <!-- Profile Dropdown -->
                    @if (Auth::guard('admin')->check())
                        <div class="relative" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 text-gray-300 hover:text-nutri-blue">
                                <i data-feather="user" class="w-5 h-5"></i>
                                <span>{{ ucfirst(Auth::guard('admin')->user()->name) }}</span>
                            </button>
                            <div x-show="profileOpen" @click.away="profileOpen = false" class="absolute right-0 mt-2 w-48 bg-nutri-dark dark:bg-gray-800 rounded-lg shadow-xl p-2" x-transition:enter="transition fade-in duration-200" x-transition:leave="transition fade-in duration-200">
                                <a href="/settings" class="block px-4 py-2 text-gray-300 hover:text-nutri-blue rounded-md">{{ __('Profile') }}</a>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-300 hover:text-nutri-blue rounded-md">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('admin.login') }}" class="text-gray-300 hover:text-nutri-blue">{{ __('Login') }}</a>
                    @endif
                    <!-- Language Dropdown -->
                    <select x-model="selectedLanguage" @change="changeLanguage($event)" class="text-sm bg-nutri-dark dark:bg-gray-800 text-white rounded-lg p-2 focus:ring-2 focus:ring-nutri-blue">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                        <option value="ko" {{ app()->getLocale() == 'ko' ? 'selected' : '' }}>{{ __('Korean') }}</option>
                        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>{{ __('Spanish') }}</option>
                    </select>
                </div>
            </div>
            <!-- Mobile Navigation Dropdown -->
            <nav class="lg:hidden bg-nutri-dark dark:bg-gray-800 border-t border-gray-700" x-show="navOpen" x-transition:enter="transition slide-up duration-300" x-transition:leave="transition slide-up duration-300" x-cloak>
                <div class="flex flex-col p-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item relative flex items-center gap-2 text-nutri-blue bg-gray-700 px-3 py-2 rounded-md font-semibold">
                        <i data-feather="home" class="w-5 h-5"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                    <a href="{{ route('admin.emails') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="mail" class="w-5 h-5"></i>
                        <span>{{ __('Emails') }}</span>
                    </a>
                    <a href="{{ route('admin.analysis') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                        <span>{{ __('Analysis') }}</span>
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="tag" class="w-5 h-5"></i>
                        <span>{{ __('Coupons') }}</span>
                    </a>
                    <a href="{{ route('admin.coupons.create') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="plus-circle" class="w-5 h-5"></i>
                        <span>{{ __('Create Coupon') }}</span>
                    </a>
                    <a href="{{ route('admin.shares.index') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="share-2" class="w-5 h-5"></i>
                        <span>{{ __('Shares') }}</span>
                    </a>
                    <a href="{{ route('admin.foods.index') }}" class="nav-item relative flex items-center gap-2 text-gray-300 hover:text-nutri-blue px-3 py-2 rounded-md">
                        <i data-feather="apple" class="w-5 h-5"></i>
                        <span>{{ __('Foods') }}</span>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto mt-10 px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 dark:border-green-400 text-green-700 dark:text-green-200 p-4 mb-6 rounded-lg animate-fade-in">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 dark:bg-red-800 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-200 p-4 mb-6 rounded-lg animate-fade-in">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-nutri-dark dark:bg-gray-800/95 backdrop-blur-sm py-8 mt-16 shadow-inner">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8 px-4 sm:px-6 lg:px-8 text-center md:text-left">
            <div>
                <h3 class="text-2xl font-bold mb-4 text-nutri-blue">{{ __('NutriSnap') }}</h3>
                <p class="text-sm text-gray-400">{{ __('AI-powered calorie tracking for a healthier you.') }}</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-4 text-white">{{ __('Quick Links') }}</h3>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-sm text-gray-400 hover:text-nutri-blue">{{ __('Features') }}</a></li>
                    <li><a href="#pricing" class="text-sm text-gray-400 hover:text-nutri-blue">{{ __('Pricing') }}</a></li>
                    <li><a href="#contact" class="text-sm text-gray-400 hover:text-nutri-blue">{{ __('Contact') }}</a></li>
                    <li><a href="/privacy" class="text-sm text-gray-400 hover:text-nutri-blue">{{ __('Privacy') }}</a></li>
                    <li><a href="/terms" class="text-sm text-gray-400 hover:text-nutri-blue">{{ __('Terms') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-4 text-white">{{ __('Follow Us') }}</h3>
                <div class="flex justify-center md:justify-start space-x-4">
                    <a href="#" aria-label="{{ __('Twitter') }}" class="text-nutri-blue hover:text-blue-400 transition-transform transform hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                    </a>
                    <a href="#" aria-label="{{ __('Instagram') }}" class="text-nutri-blue hover:text-blue-400 transition-transform transform hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5a4.25 4.25 0 00-4.25 4.25v8.5a4.25 4.25 0 004.25 4.25h8.5a4.25 4.25 0 004.25-4.25v-8.5a4.25 4.25 0 00-4.25-4.25h-8.5zm8.75 2a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5zm-3.75 1.25a5 5 0 110 10 5 5 0 010-10zm0 1.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z"/></svg>
                    </a>
                    <a href="#" aria-label="{{ __('Facebook') }}" class="text-nutri-blue hover:text-blue-400 transition-transform transform hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.597 0 0 .597 0 1.325v21.351C0 23.403.597 24 1.325 24h11.495v-9.294H9.691v-3.622h3.129V7.41c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.794.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.311h3.59l-.467 3.622h-3.123V24h6.116c.728 0 1.325-.597 1.325-1.324V1.325C24 .597 23.403 0 22.675 0z"/></svg>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-4 text-white">{{ __('Stay Updated') }}</h3>
                <div class="flex flex-col gap-3">
                    <input type="email" placeholder="{{ __('Your email') }}" class="p-3 rounded-lg bg-nutri-dark dark:bg-gray-800 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-nutri-blue">
                    <button type="button" class="bg-nutri-blue hover:bg-blue-700 text-white p-3 rounded-lg">{{ __('Subscribe') }}</button>
                </div>
            </div>
        </div>
        <div class="mt-8 text-center text-sm text-gray-400">
            &copy; 2025 {{ __('NutriSnap') }}. {{ __('All rights reserved.') }}
        </div>
    </footer>

    <!-- FAB -->
    <div class="fixed bottom-10 right-10 z-30" x-data="{ fabTooltip: false }">
        <a href="{{ route('admin.dashboard') }}" class="bg-nutri-blue text-white p-4 rounded-full shadow-lg hover:bg-blue-700" @mouseenter="fabTooltip = true" @mouseleave="fabTooltip = false" aria-label="{{ __('Admin Dashboard') }}">
            <i data-feather="home" class="w-6 h-6"></i>
        </a>
        <span x-show="fabTooltip" class="absolute bottom-14 right-0 bg-nutri-dark dark:bg-gray-800 text-white px-3 py-1 rounded-lg shadow-lg" x-transition.opacity>{{ __('Admin Dashboard') }}</span>
    </div>

    <!-- Back-to-Top -->
    <button @click="window.scrollTo({top: 0, behavior: 'smooth'})" class="fixed bottom-24 right-10 bg-nutri-blue text-white p-3 rounded-full shadow-lg hover:bg-blue-700 opacity-0 transition-opacity duration-300" :class="{ 'opacity-100': window.scrollY > 100 }" aria-label="{{ __('Back to Top') }}">
        <i data-feather="arrow-up" class="w-5 h-5"></i>
    </button>

    <script>
        // Initialize Feather icons
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof feather !== 'undefined') {
                feather.replace();
            } else {
                console.error('Feather icons failed to load');
            }
        });

        // Preloader
        document.addEventListener('DOMContentLoaded', () => {
            const preloader = document.getElementById('preloader');
            setTimeout(() => preloader.classList.add('hidden'), 1000);
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
                body: JSON.stringify({ lang })
            }).then(response => {
                if (response.ok) {
                    localStorage.setItem('language', lang);
                    window.location.reload();
                } else {
                    console.error('Language change failed');
                }
            }).catch(error => {
                console.error('Language change error:', error);
            });
        }

        // Fallback for mobile navigation if Alpine.js fails
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Alpine === 'undefined') {
                console.error('Alpine.js failed to load. Using fallback navigation toggle.');
                const navToggle = document.querySelector('header button.lg\\:hidden');
                const navMenu = document.querySelector('nav.lg\\:hidden');
                const hamburger = document.querySelector('.hamburger');
                if (navToggle && navMenu && hamburger) {
                    navToggle.addEventListener('click', () => {
                        navMenu.classList.toggle('hidden');
                        hamburger.classList.toggle('open');
                    });
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html>