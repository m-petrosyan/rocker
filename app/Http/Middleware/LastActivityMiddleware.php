<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $user = auth()->user();

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
