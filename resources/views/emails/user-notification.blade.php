<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="max-w-2xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <!-- Header -->
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $subject ?? 'Notification' }}</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">From {{ config('app.name') }}</p>
        </div>

        <!-- Message Body -->
        <div class="mb-6">
            <p class="text-gray-700 dark:text-gray-200 leading-relaxed">{!! nl2br(e($emailContent)) !!}</p>
        </div>

        <!-- Footer -->
        <div class="text-center border-t pt-4 border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-300">Thanks for being a part of {{ config('app.name') }}!</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">If you have any questions, contact us at <a href="mailto:support@{{ config('app.url') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">support@{{ config('app.url') }}</a>.</p>
        </div>
    </div>
</body>
</html>