<?php

namespace App\Http\Controllers\Profile;

use App\Repositories\UserRepository;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function __invoke(): Response
    {
        return Inertia::render('Profile/Dashboard/Dashboard', [
            'users' => UserRepository::usersList(),
        ]);
    }
}
