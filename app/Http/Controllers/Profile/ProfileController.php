<?php

namespace App\Http\Controllers\Profile;

use App\Enums\CityEnum;
use App\Enums\CountryEnum;
use App\Enums\EventGenreEnum;
use App\Http\Requests\Profile\ProfileImageUpdateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Repositories\BandRepository;
use App\Repositories\BlogRepository;
use App\Repositories\EventRepository;
use App\Repositories\GalleryReoisitory;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController
{
    public function __construct(protected UserService $userService)
    {
    }

    public function index(): Response
    {
        if (!request()->route('username')) {
//            dd(1);
            $user = auth()?->user();
            $owner = true;
            if (!$user) {
                abort(404);
            }
        } else {
//            dd(3);
            $user = User::query()->where('username', request()->route('username'))->first();
            $owner = false;
            if ($user) {
                $owner = !request()->route('username') || auth()?->user()?->username === request()->route('username');
            } else {
                abort(404);
            }
        }


        return Inertia::render('Profile/Profile', [
            'user' => $user->load('links', 'settings'),
            'owner' => $owner,
            'url' => $owner ? route('profile.show', ['username' => $user->username]) : null,
            'galleries' => GalleryReoisitory::userGallery($user),
            'events' => EventRepository::userEvents(),
            'bands' => BandRepository::userBands($user),
            'blogs' => BlogRepository::userBlogs($user),
        ]);
    }

    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Settings/Settings', [
            'user' => auth()->user()->load('links', 'settings'),
            'owner' => true,
            'countries' => CountryEnum::getKeysValues(),
            'cities' => CityEnum::getKeysValues(options: $request->country ?? auth()->user()->settings?->country),
            'genres' => EventGenreEnum::getKeysValues(),

        ]);
    }

    public function update(UserUpdateRequest $request): RedirectResponse
    {
        $this->userService->update($request->validated());

        session()->flash('message', 'Data has been successfully updated.');

        return redirect()->route('profile.index');
    }

    public function updateImage(ProfileImageUpdateRequest $request): void
    {
        auth()->user()->clearMediaCollection('images');

        auth()->user()->addMedia($request['image'])
            ->toMediaCollection('images');

        session()->flash('message', 'The image has been updated.');
    }
}
