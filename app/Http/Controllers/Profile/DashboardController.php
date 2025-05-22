<?php

namespace App\Http\Controllers\Profile;

use App\Repositories\BandRepository;
use App\Repositories\BlogRepository;
use App\Repositories\EventReoisutiry;
use App\Repositories\GalleryReoisitory;
use App\Repositories\UserRepository;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function __invoke(): Response
    {
        return Inertia::render('Profile/Dashboard/Dashboard', [
            'users' => UserRepository::usersList(),
            'statistics' => [
                'users' => UserRepository::count(),
                'events' => EventReoisutiry::count(),
                'galleries' => GalleryReoisitory::count(),
                'bands' => BandRepository::count(),
                'blogs' => BlogRepository::count(),
            ],
        ]);
    }
}
