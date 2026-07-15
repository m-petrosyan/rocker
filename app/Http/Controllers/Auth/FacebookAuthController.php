<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class FacebookAuthController
{
    /**
     * Redirect the user to Facebook for authentication.
     */
    public function redirect(): RedirectResponse
    {
        $redirectUrl = Socialite::driver('facebook')
            ->scopes(['public_profile', 'email'])
            ->redirectUrl(config('services.facebook.redirect'))
            ->redirect()
            ->getTargetUrl();

        return redirect($redirectUrl);
    }

    /**
     * Handle the callback from Facebook.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error') || $request->has('error_code')) {
            Log::warning('FacebookAuth: user denied or error', [
                'error' => $request->get('error'),
                'error_description' => $request->get('error_description'),
            ]);

            return redirect()->route('profile.index')
                ->withErrors(['facebook' => 'Авторизация через Facebook отменена.']);
        }

        try {
            $fbUser = Socialite::driver('facebook')
                ->redirectUrl(config('services.facebook.redirect'))
                ->user();

            $token = $fbUser->token;
            $fbUserId = $fbUser->getId();

            if (! $token) {
                return redirect()->route('profile.index')
                    ->withErrors(['facebook' => 'Не удалось получить токен Facebook.']);
            }

            // Store token in user settings
            $user = auth()->user();
            $user->settings()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'fb_user_id' => $fbUserId,
                    'fb_user_token' => $token,
                ]
            );

            session()->flash('message', '✅ Facebook аккаунт подключён! Теперь бот может собирать ивенты.');

            Log::info('FacebookAuth: token saved', ['user_id' => $user->id]);
        } catch (\Throwable $e) {
            Log::error('FacebookAuth: callback error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('profile.index')
                ->withErrors(['facebook' => 'Ошибка при подключении Facebook: '.$e->getMessage()]);
        }

        return redirect()->route('profile.index');
    }
}
