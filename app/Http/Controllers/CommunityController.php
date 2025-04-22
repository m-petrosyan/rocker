<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CommunityController
{
    public function index(): Response
    {
        return Inertia::render('Community/Community');
    }
}
