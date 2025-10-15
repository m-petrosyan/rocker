<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LastActivityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        if ($request->is('telegraph/*/webhook')) {
//            Log::info('telegraph/*/webhook');
//
//            return $next($request);
//        }

        $user = auth()->user();

        Log::info('last activity: '.$user);

        if ($user && (!$user->last_activity || ($user->last_activity && now()->subMinutes('2')->gte(
                        $user->last_activity
                    )))) {
            $user->update([
                'last_activity' => now(),
            ]);
        }

        return $next($request);
    }
}
