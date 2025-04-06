<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserRegisterService
{
    public function store(array $attributes): void
    {
        $user = User::query()
            ->create($attributes);
        //
//        ->assignRole('user')

        $user->createToken('auth_token')->plainTextToken;
    }

    public function login(): string
    {
        $user = auth()->user();
        $user->tokens()->delete();

        return $user->createToken('auth_token')->plainTextToken;
    }

    public function verify(array $attributes): bool
    {
        $userVerifications = UserVerify::all();
        $isMatch = false;

        foreach ($userVerifications as $verification) {
            if (
                Hash::check($attributes['code'], $verification->hash) &&
                Carbon::parse($verification->expires_at)->isFuture()
            ) {
                auth()->loginUsingId($verification->user_id);
                $isMatch = true;
                break;
            }
        }

        return $isMatch;
    }

    public function emailSender(object $request): void
    {
        if (!session()->has('email')) {
            if ($request?->user() && !$request->query('code')) {
                $user = $request->user();
                event(new Registered($user));
                session()->put('email', $user->email);
            }
        }
    }
}
