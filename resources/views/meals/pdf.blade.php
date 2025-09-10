@extends('layouts.app1')

@section('head')
    <meta charset="UTF-8">
    <meta property="og:title" content="{{ __('My :type Analysis with NutriSnap', ['type' => ucfirst($meal->meal_type)]) }}">
    <meta property="og:description" content="{{ __('Check out my :type analysis! Approx. :calories kcal. :feedback #NutriSnap #HealthyEating', ['type' => $meal->meal_type, 'calories' => $meal->calories ?? 'N/A', 'feedback' => $meal->feedback ?? 'N/A']) }}">
    <meta property="og:image" content="{{ $meal->photo_url ? Storage::url($meal->photo_url) : asset('images/nutrisnap-logo.png') }}">
    <meta property="og:url" content="{{ route('results.public', $meal->uuid) }}">
    <meta property="og:type" content="website">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #ffffff;
            position: relative;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.1;
            font-size: 4rem;
            color: #4b5563;
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 80px;
        }
        .section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .text-red {
            color: red;
        }
        h1 {
            font-size: 2rem;
            color: #1f2937;
        }
        h2 {
            font-size: 1.5rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1rem;
            color: #4b5563;
            margin: 0.5rem 0;
        }
    </style>
@endsection

@section('content')
    <div class="relative min-h-screen bg-white">
        <div class="watermark">{{ __('NutriSnap') }}</div>
        <div class="container">
            <div class="header">
                <img src="{{ asset('images/nutrisnap-logo.png') }}" alt="{{ __('NutriSnap Logo') }}">
                <h1>{{ __('title') }}</h1>
                <p>{{ __('company_info') }}</p>
                <p>{{ __('email') }}</p>
                <p>{{ __('generated_on', ['date' => now()->format('M d, Y')]) }}</p>
            </div>

            <div class="section image-container">
                <h2>{{ __('meal_images') }}</h2>
                <div class="slider-track h-full">
                    @if ($meal->photo_url)
                        <img src="{{ $meal->photo_url ? Storage::url($meal->photo_url) : asset('images/placeholder.jpg') }}" alt="{{ __('meal_images') }}" class="object-cover w-full h-full" onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                    @else
                        <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ __('NutriSnap Logo') }}" class="object-cover w-full h-full">
                        <p>{{ __('no_meal_photo') }}</p>
                    @endif
                    @if ($meal->leftover_photo_url)
                        <img src="{{ $meal->leftover_photo_url ? Storage::url($meal->leftover_photo_url) : asset('images/placeholder.jpg') }}" alt="{{ __('Leftover Photo') }}" class="object-cover w-full h-full" onerror="this.src='{{ asset('images/placeholder.jpg') }}';">
                    @else
                        <p>{{ __('no_leftover_photo') }}</p>
                    @endif
                </div>
            </div>

            <div class="section">
                <h2>{{ __('analysis_result', ['id' => $meal->id]) }}</h2>
                <div class="grid">
                    <div>
                        <p>{{ __('user', ['name' => $meal->user->name ?? 'N/A']) }}</p>
                        <p>{{ __('meal_type', ['type' => ucfirst($meal->meal_type ?? 'N/A')]) }}</p>
                        <p>{{ __('calories', ['calories' => $meal->calories ?? 'N/A']) }}</p>
                        <p>{{ __('portion_size', ['size' => $meal->portion_size ?? 'N/A']) }}</p>
                        <p>{{ __('health_condition', ['condition' => ucfirst($meal->health_condition ?? 'N/A')]) }}</p>
                        <p>{{ __('status', ['status' => ucfirst($meal->status ?? 'Pending')]) }}</p>
                        <p>{{ __('created_at', ['date' => $meal->created_at ? $meal->created_at->format('M d, Y H:i') : 'N/A']) }}</p>
                        <p>{{ __('updated_at', ['date' => $meal->updated_at ? $meal->updated_at->format('M d, Y H:i') : 'N/A']) }}</p>
                    </div>
                    <div>
                        <p>{{ __('platform', ['platform' => ucfirst($meal->platform ?? 'N/A')]) }}</p>
                        <p>{{ __('correction_request', ['request' => $meal->correction_request ?? 'N/A']) }}</p>
                        <p>{{ __('corrected_calories', ['calories' => $meal->corrected_calories ?? 'N/A']) }}</p>
                        <p>{{ __('corrected_food', ['food' => $meal->corrected_food ?? 'N/A']) }}</p>
                        <p>{{ __('share_link', ['link' => $meal->share_link['public'] ?? 'N/A']) }}</p>
                        <p>{{ !empty($meal->share_proof) ? __('share_proof', ['count' => count($meal->share_proof)]) : __('share_proof_none') }}</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>{{ __('feedback') }}</h2>
                <p class="{{ $meal->status === 'rejected' ? 'text-red' : '' }}">{{ $meal->feedback ?? __('N/A') }}</p>
            </div>

            <div class="section">
                <h2>{{ __('analysis') }}</h2>
                @if (is_array($meal->analysis) && !empty($meal->analysis))
                    <div class="grid">
                        @foreach ($meal->analysis as $key => $value)
                            <p><strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</p>
                        @endforeach
                    </div>
                @else
                    <p>{{ __('N/A') }}</p>
                @endif
            </div>

            <div class="section">
                <h2>{{ __('leftover_analysis') }}</h2>
                @if (is_array($meal->leftover_analysis) && !empty($meal->leftover_analysis))
                    <div class="grid">
                        @foreach ($meal->leftover_analysis as $key => $value)
                            <p><strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</p>
                        @endforeach
                    </div>
                @else
                    <p>{{ __('N/A') }}</p>
                @endif
            </div>

            <div class="section">
                <h2>{{ __('share_proof') }}</h2>
                @if (is_array($meal->share_proof) && !empty($meal->share_proof))
                    <div class="grid">
                        @foreach ($meal->share_proof as $key => $value)
                            <p><strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</p>
                        @endforeach
                    </div>
                @else
                    <p>{{ __('N/A') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection