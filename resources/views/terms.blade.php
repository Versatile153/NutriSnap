@extends('layouts.app')

@section('content')
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] },
                    colors: { border: '#4B5563', 'nutri-pink': '#F472B6' }
                }
            }
        }
    </script>

    <style>
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.15;
            font-size: 6rem;
            font-weight: 800;
            color: #F472B6;
            transform: rotate(-30deg);
            pointer-events: none;
            z-index: 10;
            animation: pulse 3s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen">
    <!-- Watermark -->
    <div class="watermark">{{ __('NutriSnap AI') }}</div>

    <!-- Header -->
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

    <!-- Main Content -->
    <main class="mx-auto max-w-[800px] px-4 sm:px-6 py-10">
        <h1 class="text-3xl font-bold text-white mb-6">{{ __('Terms and Conditions') }}</h1>
        <p class="text-gray-400 mb-4">{{ __('Last Updated:') }} {{ now()->format('F d, Y, h:i A T') }}</p>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Acceptance of Terms') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('By accessing or using the NutriSnap Service (the "Service"), you agree to be bound by these Terms and Conditions, our Privacy Policy, and all applicable laws and regulations.') }}</p>
            <p class="text-gray-300">{{ __('If you do not agree with any part of these terms, you may not use the Service.') }}</p>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Use of the Service') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('You may use the Service only for lawful purposes and in accordance with these Terms:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('You must be at least 13 years old to use the Service.') }}</li>
                <li>{{ __('You are responsible for maintaining the confidentiality of your account credentials.') }}</li>
                <li>{{ __('You agree not to use the Service to upload harmful, illegal, or infringing content.') }}</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Subscription and Payments') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('The Service offers free and paid subscription plans. By subscribing, you agree to:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('Pay the applicable fees as described on our pricing page.') }}</li>
                <li>{{ __('Automatic renewal of your subscription unless canceled.') }}</li>
                <li>{{ __('No refunds except as required by law.') }}</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Intellectual Property') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('All content, features, and functionality of the Service are owned by NutriSnap and protected by intellectual property laws:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('You may not reproduce, distribute, or create derivative works without our permission.') }}</li>
                <li>{{ __('NutriSnap retains all rights to the AI models and analysis results.') }}</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Termination') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('We may terminate or suspend your account at our discretion if you violate these Terms:') }}</p>
            <p class="text-gray-300">{{ __('Upon termination, your right to use the Service will cease, and we may retain certain data as required by law.') }}</p>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Limitation of Liability') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('NutriSnap is not liable for any indirect, incidental, or consequential damages arising from your use of the Service.') }}</p>
            <p class="text-gray-300">{{ __('The Service is provided "as is" without warranties of any kind.') }}</p>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Governing Law') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('These Terms are governed by the laws of Nigeria. Any disputes will be resolved in Lagos courts.') }}</p>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Changes to Terms') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('We may update these Terms periodically. Continued use after changes indicates your acceptance.') }}</p>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Contact Us') }}</h2>
            <p class="text-gray-300">{{ __('For questions or concerns, contact us at:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('Email: support@nutrisnap.com') }}</li>
                <li>{{ __('Address: NutriSnap, 123 Health Lane, Lagos, Nigeria') }}</li>
            </ul>
        </section>
    </main>

    <script>
        feather.replace();
    </script>
@endsection