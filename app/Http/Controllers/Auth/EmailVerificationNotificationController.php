<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EmailVerificationRequest;
use App\Services\UserRegisterService;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function __construct(protected UserRegisterService $userRegisterService)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                route('profile.index', ['username' => auth()->user()->username], absolute: false)
            );
        }

        $limiter = app(RateLimiter::class);
        $key = 'email-verification:'.$request->user()->id;

        if ($limiter->tooManyAttempts($key, 2, 1)) {
            $seconds = $limiter->availableIn($key);

            return back()->withErrors(['error' => "Please wait $seconds seconds before trying again."]);
        }

        $request->user()->sendEmailVerificationNotification();

        $limiter->hit($key, 120);

        return back()->with(
            'status',
            'A new verification link has been sent to the email address you provided during registration.'
        );
    }

    public function verify(EmailVerificationRequest $emailVerificationRequest): RedirectResponse
    {
        $isMatch = $this->userRegisterService->verify($emailVerificationRequest->validated());

        if (!$isMatch) {
            return back()->withErrors(['error' => "Invalid or expired verification code."]);
        }

        if (auth()->user()->hasVerifiedEmail()) {
            return back()->withErrors(['error' => "Email already verified."]);
        }

        session()->flash('message', 'Email successfully verified.');

        auth()->user()->markEmailAsVerified();
        auth()->user()->verification()->delete();

        return redirect()->route('profile.show', ['username' => auth()->user()->username])
            ->with('status', 'Email successfully verified.');
    }
}
