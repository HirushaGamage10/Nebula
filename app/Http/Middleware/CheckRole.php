<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user is active
        if ($user->status != "1") {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is not active.');
        }

        // If no specific roles are required, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (in_array($user->user_role, $roles)) {
            return $next($request);
        }

        // User doesn't have required role
        if ($request->expectsJson()) {
            return response()->json(['error' => 'You do not have permission to access this resource.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
    }
} 