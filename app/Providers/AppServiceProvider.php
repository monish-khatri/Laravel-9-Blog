<?php

namespace App\Providers;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EnsureUserHasRole::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Disabling Input Normalization

        TrimStrings::skipWhen(function ($request) {
            return $request->is('blogs/*');
        });
        ConvertEmptyStringsToNull::skipWhen(function ($request) {
            return $request->is('blogs/*');
        });
    }
}
