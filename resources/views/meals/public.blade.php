@extends('layouts.app1')

@section('head')
    <meta property="og:title" content="{{ __('My :type Analysis with NutriSnap', ['type' => ucfirst($meal->meal_type)]) }}">
    <meta property="og:description" content="{{ __('Check out my :type analysis! Approx. :calories kcal. :feedback #NutriSnap #HealthyEating', ['type' => $meal->meal_type, 'calories' => $meal->calories ?? 'N/A', 'feedback' => $meal->feedback ?? 'N/A']) }}">
    <meta property="og:image" content="{{ $meal->photo_url ? Storage::url($meal->photo_url) : asset('images/nutrisnap-logo.png') }}">
    <meta property="og:url" content="{{ route('results.public', $meal->uuid) }}">
    <meta property="og:type" content="website">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.05;
            font-size: 2.5rem;
            color: #4b5563;
            pointer-events: none;
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .section {
            margin-bottom: 1.5rem;
            padding: 1rem sm:p-1.5rem;
            background: #f9fafb;
            border-radius: 0.5rem;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .btn {
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('content')
    <div class="relative min-h-screen bg-white">
        <div class="watermark">{{ __('NutriSnap') }}</div>
        <div class="container max-w-5xl mx-auto p-4 sm:p-8 mt-6 sm:mt-10">
            <!-- Company Letterhead -->
            <header class="text-center mb-6 sm:mb-8 border-b pb-4">
                <h1 class="text-2xl sm:text-4xl font-bold text-gray-800">{{ __('title') }}</h1>
                <p class="text-gray-600 text-sm sm:text-base">{{ __('company_info') }}</p>
                <p class="text-gray-600 text-sm sm:text-base">{{ __('email') }}</p>
                <p class="text-gray-600 text-sm sm:text-base">{{ __('generated_on', ['date' => now()->format('M d, Y')]) }}</p>
            </header>

            <!-- Images -->
            <section class="section image-container">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">{{ __('meal_images') }}</h2>
                <div class="slider-track h-full">
                    @if ($meal->photo_url)
                        <img src="{{ $meal->photo_url ? Storage::url($meal->photo_url) : asset('images/placeholder.jpg') }}" alt="{{ __('meal_images') }}" class="object-cover w-full h-full" onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                    @else
                        <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ __('NutriSnap') }}" class="object-cover w-full h-full">
                        <p class="text-gray-500 text-sm sm:text-base">{{ __('no_meal_photo') }}</p>
                    @endif
                    @if ($meal->leftover_photo_url)
                        <img src="{{ $meal->leftover_photo_url ? Storage::url($meal->leftover_photo_url) : asset('images/placeholder.jpg') }}" alt="{{ __('Leftover Photo') }}" class="object-cover w-full h-full mt-4" onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                    @else
                        <p class="text-gray-500 text-sm sm:text-base mt-4">{{ __('no_leftover_photo') }}</p>
                    @endif
                </div>
            </section>

            <!-- Analysis Details -->
            <section class="section">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">{{ __('analysis_result', ['id' => $meal->id]) }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('user', ['name' => $meal->user->name ?? 'N/A']) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('meal_type', ['type' => ucfirst($meal->meal_type ?? 'N/A')]) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('calories', ['calories' => $meal->calories ?? 'N/A']) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('portion_size', ['size' => $meal->portion_size ?? 'N/A']) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('health_condition', ['condition' => ucfirst($meal->health_condition ?? 'N/A')]) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('status', ['status' => ucfirst($meal->status ?? 'Pending')]) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('created_at', ['date' => $meal->created_at ? $meal->created_at->format('M d, Y H:i') : 'N/A']) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('updated_at', ['date' => $meal->updated_at ? $meal->updated_at->format('M d, Y H:i') : 'N/A']) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('platform', ['platform' => ucfirst($meal->platform ?? 'N/A')]) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('correction_request', ['request' => $meal->correction_request ?? 'N/A']) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('corrected_calories', ['calories' => $meal->corrected_calories ?? 'N/A']) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('corrected_food', ['food' => $meal->corrected_food ?? 'N/A']) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ __('share_link', ['link' => $meal->share_link['public'] ?? 'N/A']) }}</p>
                        <p class="text-gray-700 text-sm sm:text-base">{{ !empty($meal->share_proof) ? __('share_proof', ['count' => count($meal->share_proof)]) : __('share_proof_none') }}</p>
                    </div>
                </div>
            </section>

            <!-- Feedback -->
            <section class="section">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">{{ __('feedback') }}</h2>
                <p class="text-gray-700 text-sm sm:text-base {{ $meal->status === 'rejected' ? 'text-red-600' : '' }}">{{ $meal->feedback ?? __('N/A') }}</p>
            </section>

            <!-- Analysis -->
            <section class="section">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">{{ __('analysis') }}</h2>
                @if (is_array($meal->analysis) && !empty($meal->analysis))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        @foreach ($meal->analysis as $key => $value)
                            <p class="text-gray-700 text-sm sm:text-base"><strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-700 text-sm sm:text-base">{{ __('N/A') }}</p>
                @endif
            </section>

            <!-- Leftover Analysis -->
            <section class="section">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">{{ __('leftover_analysis') }}</h2>
                @if (is_array($meal->leftover_analysis) && !empty($meal->leftover_analysis))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        @foreach ($meal->leftover_analysis as $key => $value)
                            <p class="text-gray-700 text-sm sm:text-base"><strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-700 text-sm sm:text-base">{{ __('N/A') }}</p>
                @endif
            </section>

            <!-- Share Proof -->
            <section class="section">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">{{ __('share_proof') }}</h2>
                @if (is_array($meal->share_proof) && !empty($meal->share_proof))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        @foreach ($meal->share_proof as $key => $value)
                            <p class="text-gray-700 text-sm sm:text-base"><strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-700 text-sm sm:text-base">{{ __('N/A') }}</p>
                @endif
            </section>

            <!-- Share Options -->
            <section class="text-center">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">{{ __('share_results') }}</h3>
                <div class="flex flex-wrap justify-center gap-2 sm:gap-4">
                    <a href="https://x.com/intent/tweet?url={{ urlencode(route('results.public', $meal->uuid)) }}&text={{ urlencode(__('twitter_share', ['calories' => $meal->calories ?? 'N/A'])) }}" class="bg-blue-500 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-blue-600 btn">Twitter/X</a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('results.public', $meal->uuid)) }}" class="bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-blue-700 btn">Facebook</a>
                    <a href="#" id="instagramShare" class="bg-pink-500 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-pink-600 btn">Instagram</a>
                </div>
                <div class="mt-4">
                    <button id="generateShareImage" data-uuid="{{ $meal->uuid }}" class="bg-green-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-green-600 btn transition-all duration-300">{{ __('generate_share_image') }}</button>
                </div>
                <!--<div class="mt-4">-->
                <!--    <a href="{{ route('results.download', $meal->uuid) }}" class="bg-purple-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-purple-700 btn transition-all duration-300">{{ __('download_pdf_report') }}</a>-->
                <!--</div>-->
            </section>
        </div>
    </div>

    <script>
        // Instagram Share Button
        document.getElementById('instagramShare')?.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ __('instagram_share_title') }}',
                text: '{{ __('instagram_share_text') }}',
                icon: 'info',
                confirmButtonText: '{{ __('ok') }}',
                confirmButtonColor: '#3b82f6'
            });
        });

        // Generate Share Image Button
        document.getElementById('generateShareImage')?.addEventListener('click', async function () {
            const uuid = this.getAttribute('data-uuid');
            try {
                const response = await fetch("{{ route('results.generate-image', $meal->uuid) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        title: '{{ __('success') }}',
                        text: '{{ __('image_generated_success') }}',
                        icon: 'success',
                        confirmButtonText: '{{ __('download_image') }}',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.href = data.image_url;
                    });
                } else {
                    Swal.fire({
                        title: '{{ __('error') }}',
                        text: '{{ __('image_generation_failed', ['message' => 'data.message']) }}',
                        icon: 'error',
                        confirmButtonText: '{{ __('ok') }}',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: '{{ __('error') }}',
                    text: '{{ __('route_not_found') }}',
                    icon: 'error',
                    confirmButtonText: '{{ __('ok') }}',
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    </script>
@endsection