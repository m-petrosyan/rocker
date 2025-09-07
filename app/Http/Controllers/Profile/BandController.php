<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Band\BandCreateRequest;
use App\Http\Requests\Band\BandUpdateRequest;
use App\Models\Album;
use App\Models\Band;
use App\Models\Genre;
use App\Repositories\BandRepository;
use App\Services\BandService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BandController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected BandService $bandService)
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Profile/Bands/BandCreateEdit', [
            'bandsList' => BandRepository::withoutPage(),
            'genres' => Genre::query()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BandCreateRequest $request): RedirectResponse
    {
        $this->bandService->store($request->validated());

        session()->flash('message', 'Thank you, the band has been created.');

        return redirect()->route('profile.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Band $band): Response
    {
        $this->authorize('update', $band);

        return Inertia::render('Profile/Bands/BandCreateEdit', [
            'band' => $band->load('genres', 'links', 'albums'),
            'genres' => Genre::query()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Band $band, BandUpdateRequest $request): RedirectResponse
    {
        $this->authorize('update', $band);

        $this->bandService->update($band, $request->validated());

        session()->flash('message', 'The band has been updated.');

        return redirect()->route('profile.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Band $band): RedirectResponse
    {
        $this->bandService->destroy($band);

        session()->flash('message', 'The band has been deleted.');

        return redirect()->route('profile.index');
        // չենք ջնջում այլ մաքրումն ենք սաղ ինֆոն + նկարները բացի անունից
    }

    public function albumDestroy(Album $album): void
    {
        $this->bandService->destroyAlbum($album);

        session()->flash('message', 'The album has been deleted.');
    }
}
