<?php

namespace App\Http\Controllers\Profile;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController
{
    public function index(): Response
    {
        $user = auth()?->user() ?? User::query()->where('username', request()->route('username'))->first();
        $owner = false;
        if ($user) {
            $owner = !request()->route('username') || auth()?->user()->username === request()->route('username');
        }

        if (!$user && !request()->route('username')) {
            abort(404);
        }

        return Inertia::render('Profile/Profile', [
            'user' => $user,
            'owner' => $owner,
        ]);
    }
}
