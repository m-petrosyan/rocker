<?php

namespace App\Http\Controllers\Profile;

use Inertia\Inertia;
use Inertia\Response;

class GalleryController
{
    public function create(): Response
    {
        return Inertia::render('Profile/Gallery/GalleryCreateEdit');
    }

}
