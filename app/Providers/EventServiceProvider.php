<?php

namespace App\Providers;

use App\Models\Gallery;
use App\Observers\GalleryObserver;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
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
        Gallery::observe(GalleryObserver::class);
    }
}
