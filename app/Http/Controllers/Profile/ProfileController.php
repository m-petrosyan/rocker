<?php

namespace App\Http\Controllers\Profile;

use Inertia\Inertia;

class ProfileController
{
    public function index()
    {
        return Inertia::render('Profile/Profile');
    }
}
