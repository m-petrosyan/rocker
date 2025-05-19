<?php

namespace App\Providers;

use App\Models\Band;
use App\Models\Blog;
use App\Models\Gallery;
use App\Policies\OwnerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Gallery::class, OwnerPolicy::class);
        Gate::policy(Band::class, OwnerPolicy::class);
        Gate::policy(Blog::class, OwnerPolicy::class);
    }
}
