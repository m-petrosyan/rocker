<?php

namespace App\Providers;

use App\Models\Gallery;
use App\Policies\GalleryPolicy;
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
        Gate::policy(Gallery::class, GalleryPolicy::class);
    }
}
