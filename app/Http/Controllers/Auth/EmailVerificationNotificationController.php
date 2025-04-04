<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                route('profile.index', ['username' => auth()->user()->username], absolute: false)
            );
        }

        $limiter = app(\Illuminate\Cache\RateLimiter::class);
        $key = 'email-verification:'.$request->user()->id;

        if ($limiter->tooManyAttempts($key, 2, 1)) {
            $seconds = $limiter->availableIn($key);

            return Inertia::render('Auth/VerifyEmail', [
                'errors' => ['error' => "Please wait $seconds seconds before trying again."],
            ])->toResponse($request);
        }

        $request->user()->sendEmailVerificationNotification();

        $limiter->hit($key, 60);

        return back()->with('status', 'verification-link-sent');
    }
}
