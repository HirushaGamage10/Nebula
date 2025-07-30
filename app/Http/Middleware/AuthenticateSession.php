<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;

class AuthenticateSession extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Check if the user is authenticated using session
        if (! $this->authenticate($request, $guards)) {
            return $this->redirectToLogin($request);
        }

        return $next($request);
    }

    /**
     * Redirect the user to the login page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToLogin($request)
    {
        return redirect()->route('login')->with('error', 'Unauthorized. Please log in.');
    }
}
