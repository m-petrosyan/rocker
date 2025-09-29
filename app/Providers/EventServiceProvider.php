<?php

namespace App\Providers;

use App\Events\EventRequestNotifyEvent;
use App\Events\ImageSendRequested;
use App\Events\UserCreatedEvent;
use App\Listeners\EventConfirmUpdatedListener;
use App\Listeners\EventRequestNotifyListener;
use App\Listeners\SendImageListener;
use App\Listeners\UserCreatedListener;
use App\Listeners\UsersCountNotificationListener;
use App\Models\Event;
use App\Models\EventStatus;
use App\Models\Gallery;
use App\Models\UserBot;
use App\Observers\EventObserver;
use App\Observers\EventStatusObserver;
use App\Observers\GalleryObserver;
use App\Observers\UserBotObserver;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected array $listen = [
//        UserCreatedEvent::class => [
//            UserCreatedListener::class,
//            UsersCountNotificationListener::class,
//        ],
//        EventRequestNotifyEvent::class => [
//            EventRequestNotifyListener::class,
//        ],
//        ImageSendRequested::class => [
//            SendImageListener::class,
//        ],
    ];

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
        EventStatus::observe(EventStatusObserver::class);
        Gallery::observe(GalleryObserver::class);
        Event::observe(EventObserver::class);
        UserBot::observe(UserBotObserver::class);
    }
}
