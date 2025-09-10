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
        <h1 class="text-3xl font-bold text-white mb-6">{{ __('Privacy Policy') }}</h1>
        <p class="text-gray-400 mb-4">{{ __('Last Updated:') }} {{ now()->format('F d, Y, h:i A T') }}</p>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Information We Collect') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('We collect the following data to provide accurate health insights:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('Personal Information: Name, email address, and optional health conditions (e.g., diabetes, hypertension) provided during account creation or profile updates.') }}</li>
                <li>{{ __('Health and Dietary Data: Weight, height, dietary goals, and food photo uploads for calorie and ingredient analysis.') }}</li>
                <li>{{ __('Usage Data: IP address, browser type, device information, and interaction logs (e.g., meal logs, search history) collected automatically.') }}</li>
                <li>{{ __('Shared Data: Information shared via SNS for coupon issuance, subject to your consent.') }}</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('How We Use Your Information') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('We use your data to enhance your experience, including:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('Providing personalized calorie and ingredient analysis based on uploaded photos.') }}</li>
                <li>{{ __('Recommending dietary adjustments for underlying conditions (e.g., avoiding high-sugar foods for diabetes).') }}</li>
                <li>{{ __('Issuing coupons upon SNS sharing and managing subscription benefits.') }}</li>
                <li>{{ __('Improving our AI models and Service through aggregated, anonymized data.') }}</li>
                <li>{{ __('Ensuring compliance with legal obligations and responding to requests from authorities.') }}</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('How We Share Your Information') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('We share data responsibly to maintain your trust, including:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('Service Providers: Third-party vendors (e.g., cloud hosting, payment processors) under confidentiality agreements.') }}</li>
                <li>{{ __('Legal Requirements: Disclosure to comply with laws, regulations, or legal processes.') }}</li>
                <li>{{ __('Business Transfers: Data may be transferred in case of mergers or acquisitions, with notice provided.') }}</li>
                <li>{{ __('Aggregated Data: Anonymized data shared with partners for research or marketing, without identifiable details.') }}</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Data Security') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('We protect your data with robust measures, including:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('Encrypting personal and health data using industry-standard protocols (e.g., AES-256).') }}</li>
                <li>{{ __('Storing data securely with access controls and regular security audits.') }}</li>
                <li>{{ __('Complying with GDPR, CCPA, and other relevant data protection regulations.') }}</li>
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Your Rights and Choices') }}</h2>
            <p class="text-gray-300 mb-2">{{ __('You have control over your data, including:') }}</p>
            <ul class="list-disc pl-5 space-y-2 text-gray-300">
                <li>{{ __('Accessing, updating, or deleting your personal information via the Settings page.') }}</li>
                <li>{{ __('Opting out of marketing communications or data sharing for research.') }}</li>
                <li>{{ __('Requesting data portability or restriction of processing by contacting support@nutrisnap.com.') }}</li>
                <li>{{ __('Withdrawing consent for non-essential data use at any time.') }}</li>
            </ul>
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