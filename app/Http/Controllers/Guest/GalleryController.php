<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Repositories\GalleryRepository;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Gallery/Galleries', [
            'galleries' => GalleryRepository::galleryList(),
        ]);
    }

    public function show(Gallery $gallery): Response
    {
        views($gallery)->record();

        return Inertia::render('Gallery/Gallery', [
            'gallery' => GalleryRepository::getGallery($gallery),
        ]);
    }
}
