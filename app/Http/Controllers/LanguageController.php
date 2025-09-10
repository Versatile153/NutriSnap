<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    public function switchLang($lang, Request $request)
    {
        $availableLocales = config('app.available_locales', ['en']);
        Log::debug("Switching to locale: $lang, Session before: " . json_encode(Session::all()));
        
        if (in_array($lang, $availableLocales)) {
            App::setLocale($lang);
            Session::put('locale', $lang);
            Session::save(); // Explicitly save the session
            Log::debug("Locale set to: $lang, Session after: " . json_encode(Session::all()));
        } else {
            Log::warning("Invalid locale attempted: $lang");
        }
        
        return response()->json(['status' => 'success']);
    }
}