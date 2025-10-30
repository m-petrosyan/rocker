<?php

namespace App\Http\Controllers\Profile;

use App\Enums\CityEnum;
use App\Enums\CountryEnum;
use App\Enums\EventGenreEnum;
use App\Enums\EventStatusEnum;
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
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController
{
    public function __construct(protected UserService $userService)
    {
    }

    public function index(): Response
    {
        $requestedUsername = request()->route('username');

        if ($requestedUsername) {
            $user = User::where('username', $requestedUsername)->firstOrFail();
            $owner = auth()?->user()?->username === $requestedUsername;
        } else {
            $user = auth()->user() ?? abort(404);
            $owner = true;
        }

        return Inertia::render('Profile/Profile', [
            'user' => $user->load('links', 'settings'),
            'owner' => $owner,
            'galleries' => GalleryReoisitory::userGallery($user, ['views', 'allViews']),
            'events' => EventRepository::userEvents($user),
            'bands' => BandRepository::userBands($user),
            'blogs' => BlogRepository::userBlogs($user),
            'eventRequests' => Gate::allows('full-access') ? EventRepository::count(
                EventStatusEnum::PENDING->value
            ) : null,
        ]);
    }

    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Settings/Settings', [
            'user' => auth()->user()->load('links', 'settings', 'chat', 'mergeCode'),
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
