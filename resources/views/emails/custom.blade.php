<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Inter', 'Noto Serif KR', sans-serif;
            background-color: #F3F4F6;
            color: #1F2937;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #3B82F6;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 16px;
        }
        .footer {
            background-color: #1F2937;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer a {
            color: #3B82F6;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3B82F6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 16px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('NutriSnap') }}</h1>
        </div>
        <div class="content">
            <p>{{ __('Hello') }} {{ $recipientName ?? __('User') }},</p>
            <p>{{ nl2br(e($messageContent)) }}</p>
            <a href="{{ url('/') }}" class="button">{{ __('Visit NutriSnap') }}</a>
        </div>
        <div class="footer">
            <p>{{ __('NutriSnap - AI-powered calorie tracking for a healthier you.') }}</p>
            <p><a href="{{ url('/unsubscribe') }}">{{ __('Unsubscribe') }}</a> | <a href="{{ url('/privacy') }}">{{ __('Privacy Policy') }}</a></p>
            <p>&copy; 2025 {{ __('NutriSnap') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>