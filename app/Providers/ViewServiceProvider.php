<?php

namespace App\Providers;

use App\View\Composers\BlogComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        // Attaching A Composer To Multiple Views
        // View::composer(['blog.index','blog.add'], BlogComposer::class);

        // Using closure based composers...
        // View::composer('dashboard', function ($view) {
        //     //
        // });

        // View Creators
        View::creator('blog.index', BlogComposer::class);
    }
}