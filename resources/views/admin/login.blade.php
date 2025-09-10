
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Admin Login') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .language-dropdown select {
            transition: all 0.3s ease;
        }
        .language-dropdown select:focus {
            outline: none;
            ring: 2px;
            ring-color: #3B82F6;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center" x-data="{ selectedLanguage: '{{ app()->getLocale() }}' }">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Admin Login') }}</h2>

        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 dark:border-green-400 text-green-700 dark:text-green-200 p-4 mb-6 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 dark:bg-red-800 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-200 p-4 mb-6 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 dark:bg-red-800 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-200 p-4 mb-6 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Email Address') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Password') }}</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="flex justify-between items-center mb-6">
                <div class="language-dropdown">
                    <select x-model="selectedLanguage" @change="changeLanguage($event)" class="text-sm bg-gray-800 text-white rounded-lg p-2">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                        <option value="ko" {{ app()->getLocale() == 'ko' ? 'selected' : '' }}>{{ __('Korean') }}</option>
                        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>{{ __('Spanish') }}</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition">{{ __('Login') }}</button>
            </div>
        </form>

     
    </div>

    <script>
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
    </script>
</body>
</html>
