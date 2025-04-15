<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Repositories\GalleryReoisitory;
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
            'galleries' => GalleryReoisitory::allGalleries(),
        ]);
    }

    public function show(Gallery $gallery): Response
    {
        views($gallery)->record();

        return Inertia::render('Gallery/Gallery', [
            'gallery' => $gallery->load(['user']),
        ]);
    }
}
