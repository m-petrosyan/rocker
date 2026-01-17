<?php

namespace App\Http\Controllers\Profile;

use App\Models\PwaInstall;
use App\Repositories\BandRepository;
use App\Repositories\BlogRepository;
use App\Repositories\EventRepository;
use App\Repositories\GalleryReoisitory;
use App\Repositories\UserRepository;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function __invoke(): Response
    {
        return Inertia::render('Profile/Dashboard/Dashboard', [
            'users' => UserRepository::usersList(50),
            'statistics' => [
                'users_web' => UserRepository::count(),
                'users_bot' => UserRepository::count(true),
                'events' => EventRepository::count(),
                'events_active' => EventRepository::count(active: true),
                'galleries' => GalleryReoisitory::count(),
                'bands' => BandRepository::count(),
                'blogs' => BlogRepository::count(),
                'pwa' => PwaInstall::count(),
                'charts' => [
                    'users' => \App\Repositories\StatisticsRepository::getUserActivityStats(12),
                    'events' => \App\Repositories\StatisticsRepository::getEventCreationStats(12),
                ],
            ],
        ]);
    }
}
