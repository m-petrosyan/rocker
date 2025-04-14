<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Gallery\GalleryCreateRequest;
use App\Repositories\BandRepository;
use App\Services\GalleryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController
{
    public function __construct(protected GalleryService $galleryService)
    {
    }

    public function create(): Response
    {
        return Inertia::render('Profile/Gallery/GalleryCreateEdit', [
            'bandsList' => BandRepository::bandList(),
        ]);
    }

    public function store(GalleryCreateRequest $request): RedirectResponse
    {
        $this->galleryService->store($request->validated());

        session()->flash('message', 'Thank you, the gallery has been created.');

        return redirect()->route('profile.index');
    }
}
