<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Gallery\GalleryCreateRequest;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController
{
    public function create(): Response
    {
        return Inertia::render('Profile/Gallery/GalleryCreateEdit');
    }

    public function store(GalleryCreateRequest $request)
    {
        dd($request->validated());
    }
}
