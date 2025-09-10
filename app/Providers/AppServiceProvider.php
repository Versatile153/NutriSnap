<?php

namespace App\Providers;
   use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
  public function register()
{
    $this->app->singleton(AIAnalysisService::class, function () {
        return new AIAnalysisService();
    });
}

    /**
     * Bootstrap any application services.
     */


public function boot()
{
    Schema::defaultStringLength(191);
}

}
