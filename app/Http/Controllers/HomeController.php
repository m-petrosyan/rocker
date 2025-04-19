<?php

namespace App\Http\Controllers;

use App\Repositories\EventReoisutiry;
use App\Repositories\GalleryReoisitory;
use Inertia\Inertia;
use Inertia\Response;

class HomeController
{
    public function __invoke(): Response
    {
        return Inertia::render('Home', [
            'events' => EventReoisutiry::eventsList(12),
            'galleries' => GalleryReoisitory::allGalleries(),
        ]);
    }
}
