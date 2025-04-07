<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;


class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::query()->updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'username' => $this->generateUniqueUsername($googleUser->getName()),
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(uniqid()),
                ]
            );

            Auth::login($user);

            return Inertia::location(route('profile.show', [
                'username' => $user->username,
            ]));
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login error.');
        }
    }


    protected function generateUniqueUsername(string $name): string
    {
        $username = strtolower(str_replace(' ', '', $name));
        $originalUsername = $username;
        $count = 1;

        while (User::where('username', $username)->exists()) {
            $username = $originalUsername.$count;
            $count++;
        }

        return $username;
    }

}
