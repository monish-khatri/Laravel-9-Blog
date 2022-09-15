<?php

namespace App\Providers;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\TrimStrings;
use App\Services\Counter;
use App\View\Components\Alert;
use App\View\Components\AlertMessage;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Laravel\Passport\Passport;


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
        Passport::ignoreMigrations();
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

        Collection::macro('toUpper', function () {
            return $this->map(function ($value) {
                return Str::upper($value);
            });
        });

        // Sharing Data With All Views
        View::share('name', 'Messi');

        Blade::component('package-alert', Alert::class);
        Blade::component('package-alert', AlertMessage::class);

        // Pluralizer::useLanguage('spanish');

        $this->app->singleton(Counter::class, function ($app) {
            return new Counter(
                $app->make(Factory::class),
                $app->make(Session::class),
                1
            );
        });

        // $this->app->when(Counter::class)
        //     ->needs('$timeout')
        //     ->give(1);
    }
}
