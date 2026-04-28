<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

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

        // Additional hardening headers to reduce information exposure.
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('X-XSS-Protection', '0');

        // HSTS is set at the web-server layer to avoid duplicate header entries.

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Enforce cookie security flags on all outgoing cookies to satisfy scanner baselines.
        // This protects against weak env/runtime values in hosted deployments.
        $cookies = $response->headers->getCookies();
        if (!empty($cookies)) {
            $response->headers->remove('Set-Cookie');

            $host = strtolower((string) $request->getHost());
            $isLocalHost = in_array($host, ['localhost', '127.0.0.1'], true)
                || str_ends_with($host, '.test');

            $forceSecure = !$isLocalHost;
            $defaultSameSite = config('session.same_site', 'strict') ?: 'strict';

            foreach ($cookies as $cookie) {
                $secure = $forceSecure ? true : $cookie->isSecure();
                $sameSite = $cookie->getSameSite() ?: $defaultSameSite;

                // SameSite=None requires Secure=true in modern browsers.
                if (strtolower((string) $sameSite) === 'none') {
                    $secure = true;
                }

                $response->headers->setCookie(new Cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $secure,
                    true,
                    $cookie->isRaw(),
                    $sameSite
                ));
            }
        }

        return $response;
    }
}
