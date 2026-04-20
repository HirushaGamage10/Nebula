<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictDebugSurfaces
{
    /**
     * Restrict high-noise debug/dev pages in production-like environments.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!app()->environment(['local', 'development', 'staging'])) {
            abort(404);
        }

        return $next($request);
    }
}
