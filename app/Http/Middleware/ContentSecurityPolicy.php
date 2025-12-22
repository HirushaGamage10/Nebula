<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class ContentSecurityPolicy
{
    public function handle($request, Closure $next)
    {
        // 1. Generate a random nonce
        $nonce = base64_encode(random_bytes(16));

        // 2. Share it with all Blade views
        View::share('cspNonce', $nonce);

        // 3. Continue the request
        $response = $next($request);

        // 4. Set CSP header including nonce (no unsafe-inline)
        $cdnHosts = [
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
            'https://cdn-script.com',
            'https://ajax.googleapis.com',
            'https://code.jquery.com',
        ];
        $cdnList = implode(' ', $cdnHosts);

        $csp = "default-src 'self'; "
            . "script-src 'self' 'nonce-$nonce' $cdnList; "
            . "script-src-elem 'self' 'nonce-$nonce' $cdnList; "
            . "style-src 'self' 'nonce-$nonce' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
            . "style-src-elem 'self' 'nonce-$nonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; "
            . "img-src 'self' data: blob:; "
            . "connect-src 'self'; "
            . "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net; "
            . "frame-src 'self'; "
            . "media-src 'self'; "
            . "manifest-src 'self'; "
            . "worker-src 'self'; "
            . "form-action 'self'; "
            . "object-src 'none'; "
            . "base-uri 'self'; "
            . "frame-ancestors 'self';";

        // 5. Add CSP header to response
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
