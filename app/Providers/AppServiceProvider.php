<?php

namespace App\Providers;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\TrimStrings;
use App\View\Components\Alert;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;


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

        // Response Macros
        Response::macro('caps', function ($value) {
            return Response::make(strtoupper($value));
        });

        // Sharing Data With All Views
        View::share('name', 'Messi');

        Blade::component('package-alert', Alert::class);
    }
}
