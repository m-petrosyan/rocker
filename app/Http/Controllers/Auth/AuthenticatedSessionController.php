<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\UserBot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                route('profile.show', ['username' => auth()->user()->username])
            );
        } else {
            return redirect()->route('verification.notice')->with([
                'status' => 'Please verify your email address.',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function tgWebAuth(Request $request)
    {
        $userBot = UserBot::query()
            ->where('chat_id', $request->input('id'))
            ->first();

        if ($userBot && $userBot->user) {
            auth()->loginUsingId($userBot->user->id);
            $request->session()->regenerate();
 
//            return response()->json([
//                'redirect' => session()->pull('url.intended', route('home')),
//            ]);

            return response()->json([
                'redirect' => tap(session()->pull('url.intended', route('home')), function ($url) {
                    Log::info('Redirecting to intended URL: '.$url);
                }),
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function tgAuth()
    {
        Log::info('Telegraph route loaded');
        $userId = data_get(request()->input('callback_query'), 'from.id') ?? data_get(
            request()->input('message'),
            'from.id'
        );

        if ($userId) {
            $userBot = UserBot::query()->where('chat_id', $userId)->first();

            if ($userBot) {
                auth()->loginUsingId($userBot->user?->id);
            }
        }
    }
}
