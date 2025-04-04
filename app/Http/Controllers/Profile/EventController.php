<?php

namespace App\Http\Controllers\Profile;

use Inertia\Inertia;
use Inertia\Response;

class EventController
{
    public function create(): Response
    {
        return Inertia::render('Profile/Events/EventCreateEdit');
    }
}
