<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Gallery;
use App\Models\UserBot;
use App\Observers\EventObserver;
use App\Observers\GalleryObserver;
use App\Observers\UserBotObserver;
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
        Event::observe(EventObserver::class);
        UserBot::observe(UserBotObserver::class);
    }
}
