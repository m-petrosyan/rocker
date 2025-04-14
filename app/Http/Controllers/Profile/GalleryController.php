<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Gallery\GalleryCreateRequest;
use App\Http\Requests\Gallery\GalleryUpdateRequest;
use App\Models\Gallery;
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

    public function edit(Gallery $gallery): Response
    {
        return Inertia::render('Profile/Gallery/GalleryCreateEdit', [
            'gallery' => $gallery,
            'bandsList' => BandRepository::bandList(),
        ]);
    }

    public function update(GalleryUpdateRequest $request, Gallery $gallery): RedirectResponse
    {
        $this->galleryService->update($gallery, $request->validated());

        session()->flash('message', 'The gallery has been updated.');

        return redirect()->route('profile.index');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->galleryService->destroy($gallery);

        session()->flash('message', 'The gallery has been deleted.');

        return redirect()->route('profile.index');
    }
}
