<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\User;
use App\Policies\BlogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Blog::class => BlogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('isOwner', function (User $user, Blog $blog) {
            return $user->id === $blog->user_id;
        });

        /* define a admin user role */
        Gate::define('isAdmin', function($user) {
            return $user->role == User::ADMIN_ACCESS;
        });

        /* define a user role */
        Gate::define('isUser', function($user) {
            return $user->role == User::USER_ACCESS;
        });
    }
}
