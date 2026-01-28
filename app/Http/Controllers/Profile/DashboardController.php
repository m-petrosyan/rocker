<?php

namespace App\Http\Controllers\Profile;

use App\Models\PwaInstall;
use App\Repositories\BandRepository;
use App\Repositories\BlogRepository;
use App\Repositories\EventRepository;
use App\Repositories\GalleryRepository;
use App\Repositories\UserRepository;
use App\Repositories\StatisticsRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function __invoke(Request $request): Response
    {
        $type = $request->get('type', 'users');
        $filter = $request->get('filter');
        $sort = $request->get('sort', 'newest');
        $past = $request->boolean('past', false);

        $items = match ($type) {
            'bands' => BandRepository::bandList(50, $sort),
            'events' => EventRepository::eventsList(50, 1, $past, $sort),
            'galleries' => GalleryRepository::galleryList(50, $sort),
            default => UserRepository::usersList(50, $filter, $sort),
        };

        return Inertia::render('Profile/Dashboard/Dashboard', [
            'users' => $items,
            'statistics' => [
                'users_web' => UserRepository::count(),
                'users_bot' => UserRepository::count(true),
                'events' => EventRepository::count(),
                'events_active' => EventRepository::count(active: true),
                'galleries' => GalleryRepository::count(),
                'bands' => BandRepository::count(),
                'blogs' => BlogRepository::count(),
                'pwa' => PwaInstall::count(),
                'charts' => [
                    'users' => StatisticsRepository::getUserActivityStats(12),
                    'events' => StatisticsRepository::getEventCreationStats(12),
                ],
                'disk' => StatisticsRepository::getDiskStats(),
                'countries' => StatisticsRepository::getUserCountryStats(),
            ],
            'filters' => [
                'type' => $type,
                'filter' => $filter,
                'sort' => $sort,
                'past' => $past,
            ]
        ]);
    }
}
