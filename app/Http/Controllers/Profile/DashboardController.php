<?php

namespace App\Http\Controllers\Profile;

use App\Models\PwaInstall;
use App\Repositories\BandRepository;
use App\Repositories\BlogRepository;
use App\Repositories\EventRepository;
use App\Repositories\GalleryReoisitory;
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

        $items = match ($type) {
            'bands' => BandRepository::bandList(50),
            'events' => EventRepository::eventsList(50),
            'galleries' => GalleryReoisitory::galleryList(50),
            default => UserRepository::usersList(50, $filter),
        };

        return Inertia::render('Profile/Dashboard/Dashboard', [
            'users' => $items, // Сохраняем имя 'users' для совместимости или переименуем во фронте
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
                    'users' => StatisticsRepository::getUserActivityStats(12),
                    'events' => StatisticsRepository::getEventCreationStats(12),
                ],
                'disk' => StatisticsRepository::getDiskStats(),
            ],
            'filters' => [
                'type' => $type,
                'filter' => $filter,
            ]
        ]);
    }
}
