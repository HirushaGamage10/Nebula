<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        // Remove PHP version disclosure as early as possible for every request.
        @ini_set('expose_php', '0');
        header_remove('X-Powered-By');

        $response = $next($request);

        // Apply a safe baseline CSP to every response that does not already have one.
        // Regular web pages keep their richer nonce-based CSP from ContentSecurityPolicy middleware.
        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'; object-src 'none';"
            );
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), bluetooth=()'
        );

        // ZAP checks for HSTS even on responses that are otherwise valid.
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
