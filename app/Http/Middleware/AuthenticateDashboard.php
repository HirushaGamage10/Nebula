<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateDashboard
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::guard('web')->check()) {
            // If the user is authenticated and the current route is the login page, redirect to the dashboard
            if ($request->is('login')) {
                return redirect()->route('dashboard');
            }
            
            // If the user is authenticated and the current route is not the login page, proceed with the request
            return $next($request);
        }

        // If the user is not authenticated, redirect to the login page
        return redirect('login');
    }
}
