<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Repositories\BandRepository;
use App\Repositories\EventRepository;
use App\Repositories\GalleryRepository;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Home', [
            'events' => EventRepository::eventsList(12),
            'bands' => BandRepository::bandList(8),
            'galleries' => GalleryRepository::galleryList(12),
        ]);
    }
}
