<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('NutriSnap') }} - {{ __('Dashboard') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Serif+KR:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'Noto Serif KR', 'ui-sans-serif', 'system-ui'] 
                    },
                    colors: { border: '#4B5563' }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen" x-data="{ selectedLanguage: '{{ app()->getLocale() }}' }">
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="spinner"></div>
        <div class="logo">{{ __('NutriSnap') }}</div>
    </div>

    <!-- Watermark -->
    <div class="watermark">{{ __('NutriSnap AI') }}</div>

    <!-- HEADER -->
    <header class="bg-gray-800/95 backdrop-blur-sm sticky top-0 z-40 border-b border-gray-800 shadow-md">
        <div class="mx-auto max-w-[1400px] px-4 sm:px-6">
            <div class="h-16 flex items-center justify-between">
                <!-- Left: Hamburger (mobile only) -->
                <div class="flex items-center gap-3 lg:hidden">
                    <button id="menuBtn" class="inline-flex items-center justify-center w-10 h-10 rounded-md hover:bg-gray-700/50" aria-label="{{ __('Toggle Menu') }}">
                        <i data-feather="menu" class="w-6 h-6 text-gray-300"></i>
                    </button>
                </div>

                <!-- Desktop Nav aligned left -->
                <nav class="hidden lg:flex items-center gap-8 text-[14px] leading-5">
                    <a class="flex items-center gap-2 text-gray-300 hover:text-pink-400 font-medium" href="/">
                        <i data-feather="home" class="w-5 h-5"></i> {{ __('Home') }}
                    </a>
                    <a class="flex items-center gap-2 font-semibold text-pink-400 bg-gray-700 px-3 py-2 rounded-md" href="/dashboard">
                        <i data-feather="home" class="w-5 h-5"></i> {{ __('Dashboard') }}
                    </a>
                    <a class="flex items-center gap-2 text-gray-300 hover:text-pink-400 font-medium" href="{{ route('meals.index') }}">
                        <i data-feather="list" class="w-5 h-5"></i> {{ __('Meal Logs') }}
                    </a>
                    <a class="flex items-center gap-2 text-gray-300 hover:text-pink-400 font-medium" href="/docs/nutrisnap-widget">
                        <i data-feather="book" class="w-5 h-5"></i> {{ __('Docs') }}
                    </a>
                    <details class="relative">
                        <summary class="flex items-center gap-2 text-gray-300 hover:text-pink-400 font-medium list-none cursor-pointer">
                            <i data-feather="settings" class="w-5 h-5"></i> {{ __('Settings') }} <i data-feather="chevron-down" class="w-4 h-4 ml-1"></i>
                        </summary>
                        <div class="absolute bg-gray-800/95 backdrop-blur-sm p-4 rounded-xl shadow-2xl mt-3 space-y-3 animate-menuSlide left-0">
                            <a href="/settings" class="block hover:text-pink-400 p-2 rounded-lg transition-colors text-base">{{ __('Profile') }}</a>
                        </div>
                    </details>
                </nav>

                <!-- Search, Profile, and Language Dropdown (right) -->
                <div class="flex items-center gap-4">
                    <div class="relative hidden lg:block">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="search" class="w-4 h-4"></i>
                        </span>
                        <input class="search rounded-xl pl-9 pr-3 py-2 w-[400px] text-sm placeholder-gray-400 text-white" placeholder="{{ __('Search meals, goals...') }}" />
                    </div>
                    @auth
                        <div class="relative group">
                            <button class="nav-link text-lg font-medium text-gray-300 hover:text-pink-400">{{ ucfirst(auth()->user()->name) }}</button>
                            <div class="absolute hidden group-hover:block bg-gray-800/95 backdrop-blur-sm p-4 rounded-xl shadow-2xl mt-3 space-y-3 animate-menuSlide right-0">
                                <a href="/settings" class="block hover:text-pink-400 p-2 rounded-lg transition-colors text-base">{{ __('Settings') }}</a>
                                <form action="{{ route('logout') }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left hover:text-pink-400 p-2 rounded-lg transition-colors text-base">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                   
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Overlay Nav -->
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-50"></div>
    <div id="mobileNav" class="fixed top-0 right-0 w-full lg:w-72 h-full bg-gray-800 shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="mx-auto max-w-[1400px] px-4 sm:px-6">
            <div class="flex items-center justify-between h-16 border-b border-gray-700">
                <span class="font-bold text-lg text-pink-400">{{ __('Menu') }}</span>
                <button id="closeMenu"><i data-feather="x" class="w-6 h-6 text-gray-300"></i></button>
            </div>
            <div class="py-4">
                <!-- Mobile Search -->
                <div class="relative mb-5">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i data-feather="search" class="w-4 h-4"></i>
                    </span>
                    <input class="search rounded-xl pl-9 pr-3 py-2 w-full text-sm placeholder-gray-400 text-white" placeholder="{{ __('Search...') }}" />
                </div>
                <!-- Links -->
                <nav class="flex flex-col gap-3 text-[15px]">
                    <a href="/" class="flex items-center gap-2 text-gray-300 hover:text-pink-400">
                        <i data-feather="home" class="w-5 h-5"></i> {{ __('Home') }}
                    </a>
                    <a href="/dashboard" class="flex items-center gap-2 font-semibold text-pink-400 bg-gray-700 px-3 py-2 rounded-md">
                        <i data-feather="home" class="w-5 h-5"></i> {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('meals.index') }}" class="flex items-center gap-2 text-gray-300 hover:text-pink-400">
                        <i data-feather="list" class="w-5 h-5"></i> {{ __('Meal Logs') }}
                    </a>
                    <a href="/docs/nutrisnap-widget" class="flex items-center gap-2 text-gray-300 hover:text-pink-400">
                        <i data-feather="book" class="w-5 h-5"></i> {{ __('Docs') }}
                    </a>
                    <details>
                        <summary class="flex items-center gap-2 text-gray-300 hover:text-pink-400 list-none cursor-pointer">
                            <i data-feather="settings" class="w-5 h-5"></i> {{ __('Settings') }} <i data-feather="chevron-down" class="w-4 h-4 ml-1"></i>
                        </summary>
                        <div class="pl-7 space-y-3 mt-2">
                            <a href="/settings" class="block text-gray-300 hover:text-pink-400">{{ __('Profile') }}</a>
                        </div>
                    </details>
                    <!-- Language Dropdown (Mobile) -->
                    <div class="language-dropdown mt-4" x-data="{ open: false }">
                        <select x-model="selectedLanguage" @change="changeLanguage($event)" class="w-full text-sm bg-gray-800 text-white rounded-lg p-2">
                            <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                            <option value="ko" {{ app()->getLocale() == 'ko' ? 'selected' : '' }}>{{ __('Korean') }}</option>
                            <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>{{ __('Spanish') }}</option>
                        </select>
                    </div>
                </nav>
            </div>
        </div>
    </div>

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
        // Pass translations to JavaScript
        const translations = {
            intake: '{{ __('Intake') }}',
            burned: '{{ __('Burned') }}'
        };

        // Initialize Feather icons
        document.addEventListener('DOMContentLoaded', () => {
            if (window.feather) {
                feather.replace();
            }
        });

        // Mobile nav toggle
        const menuBtn = document.getElementById('menuBtn');
        const closeBtn = document.getElementById('closeMenu');
        const nav = document.getElementById('mobileNav');
        const overlay = document.getElementById('overlay');

        function openNav() {
            nav.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
        }
        function closeNav() {
            nav.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        }
        menuBtn?.addEventListener('click', openNav);
        closeBtn?.addEventListener('click', closeNav);
        overlay?.addEventListener('click', closeNav);

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
                    localStorage.setItem('language', lang);
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
            if (!localStorage.getItem('language') && !'{{ Session::get('locale') }}') {
                fetch('https://ipapi.co/json/')
                    .then(response => response.json())
                    .then(data => {
                        const country = data.country_code;
                        let lang = 'en';
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

        // Chart
        (function initChartOnce() {
            if (window.__calorieChart) return;
            const ctx = document.getElementById('calorieChart')?.getContext('2d');
            if (!ctx) return;
            window.__calorieChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['1', '5', '10', '15', '20', '25', '28'],
                    datasets: [
                        { label: translations.intake, data: [1800, 1900, 2000, 2100, 2200, 2300, 2500], backgroundColor: '#F472B6' },
                        { label: translations.burned, data: [400, 450, 500, 480, 520, 510, 500], backgroundColor: '#D1D5DB' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#4B5563' }, ticks: { color: '#D1D5DB' } },
                        x: { grid: { display: false }, ticks: { color: '#D1D5DB' } }
                    }
                }
            });
        })();

        // Dotted Auto Sliders
        document.querySelectorAll('.slider').forEach(slider => {
            const track = slider.querySelector('.slider-track');
            const slides = track?.children.length;
            if (!slides) return;
            const dotsContainer = slider.querySelector('.dots');
            let index = 0;

            // Create dots
            for (let i = 0; i < slides; i++) {
                const dot = document.createElement('div');
                dot.className = 'dot' + (i === 0 ? ' active' : '');
                dot.addEventListener('click', () => {
                    index = i;
                    update();
                    resetAutoSlide();
                });
                dotsContainer?.appendChild(dot);
            }

            function update() {
                track.style.transform = `translateX(-${index * 100}%)`;
                dotsContainer.querySelectorAll('.dot').forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
            }

            function nextSlide() {
                index = (index + 1) % slides;
                update();
            }

            let auto = setInterval(nextSlide, 3000);
            function resetAutoSlide() {
                clearInterval(auto);
                auto = setInterval(nextSlide, 3000);
            }
        });

        // Preloader Script
        document.addEventListener('DOMContentLoaded', () => {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                setTimeout(() => {
                    preloader.classList.add('hidden');
                }, 1000);
            } else {
                console.error('Preloader element not found');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>