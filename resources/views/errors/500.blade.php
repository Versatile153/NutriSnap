<!-- resources/views/errors/500.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-pink-400">500 - Server Error</h1>
        <p class="text-gray-300 mt-2">Something went wrong. Please try again later.</p>
        <a href="{{ route('meals.index') }}" class="mt-4 inline-block text-pink-400 hover:text-pink-500">Back to Meals</a>
    </div>
</body>
</html>