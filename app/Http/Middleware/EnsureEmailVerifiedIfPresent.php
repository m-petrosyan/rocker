<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailVerifiedIfPresent
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->email && !$user->hasVerifiedEmail()) {
            abort(403, 'Email not verified.');
        }

        return $next($request);
    }
}
