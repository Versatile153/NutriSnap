<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, used in notifications and
    | other places where the application name is needed.
    |
    */

    'name' => env('APP_NAME', 'NutriSnap'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the environment your application is running in,
    | affecting how services are configured. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, detailed error messages with stack traces are shown.
    | When disabled, a generic error page is displayed.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by Artisan commands and for generating URLs in the app.
    | Set this to the root of your application.
    |
    */

    'url' => env('APP_URL', 'https://bincone.apexjets.org'),

    /*
    |--------------------------------------------------------------------------
    | Asset URL
    |--------------------------------------------------------------------------
    |
    | This URL is used for serving assets like images, CSS, and JS files.
    | Set this if assets are hosted on a different domain or CDN.
    |
    */

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Specify the default timezone for PHP date and time functions.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The default locale used by the translation service provider.
    | This is the initial language for users unless overridden.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale used when the current locale is unavailable.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    |
    | List of supported locales for multilingual functionality.
    | Used by middleware to validate language switching.
    |
    */

    'available_locales' => ['en', 'ko', 'es'],

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | The locale used by the Faker library for generating fake data.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | The key used by the Illuminate encrypter service. Must be a random,
    | 32-character string for secure encryption.
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | Determines how maintenance mode is managed. The "file" driver uses a
    | local file, while "cache" allows control across multiple machines.
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | These service providers are automatically loaded on each request.
    | Add custom providers to expand application functionality.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Intervention\Image\Laravel\ServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | These aliases allow easy access to facades without full namespace paths.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        'Image' => Intervention\Image\Laravel\Facades\Image::class,
    ])->toArray(),

];
?> 