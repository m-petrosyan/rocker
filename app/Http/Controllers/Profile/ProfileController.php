<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Profile\ProfileImageUpdateRequest;
use App\Models\User;
use App\Repositories\BandRepository;
use App\Repositories\BlogRepository;
use App\Repositories\EventReoisutiry;
use App\Repositories\GalleryReoisitory;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController
{
    public function index(): Response
    {
        if (!request()->route('username')) {
            $user = auth()?->user();
            $owner = true;
            if (!$user) {
                abort(404);
            }
        } else {
            $user = User::query()->where('username', request()->route('username'))->first();
            $owner = false;
            if ($user) {
                $owner = !request()->route('username') || auth()?->user()?->username === request()->route('username');
            } else {
                abort(404);
            }
        }

        return Inertia::render('Profile/Profile', [
            'user' => $user,
            'owner' => $owner,
            'url' => $owner ? route('profile.show', ['username' => $user->username]) : null,
            'galleries' => GalleryReoisitory::userGallery($user),
            'events' => EventReoisutiry::eventsList(0, $user->events->load('views')),
            'bands' => BandRepository::userBands($user),
            'blogs' => BlogRepository::userBlogs($user),
        ]);
    }

    public function edit(): Response
    {
        return Inertia::render('Profile/Settings/Settings', [
            'user' => auth()->user(),
            'owner' => true,
        ]);
    }

    public function updateImage(ProfileImageUpdateRequest $request): void
    {
        auth()->user()->clearMediaCollection('images');

        auth()->user()->addMedia($request['image'])
            ->toMediaCollection('images');

        session()->flash('message', 'The image has been updated.');
    }
}
