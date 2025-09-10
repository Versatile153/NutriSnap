<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $availableLocales = config('app.available_locales', ['en']);
        $locale = Session::get('locale', config('app.fallback_locale', 'en'));
        Log::debug("Applying locale: $locale, Available: " . json_encode($availableLocales));

        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
        } else {
            $locale = config('app.fallback_locale', 'en');
            App::setLocale($locale);
            Log::warning("Locale $locale not available, falling back to: $locale");
        }

        Log::debug("Final locale set: " . App::getLocale());
        return $next($request);
    }
}