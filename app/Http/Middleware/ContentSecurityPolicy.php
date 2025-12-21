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

        // 4. Set CSP header including nonce
        // Allow specific third-party hosts used in views (CDN for icons and Google Fonts).
        // Use a relaxed policy for local/development so inline styles (style attributes),
        // inline scripts and some font/image origins still work while you develop.
        // In production we keep a stricter policy using the generated nonce.

        // Tight CSP: use nonces for inline <script>/<style> elements, allow only
        // explicit CDN hosts for element loads, and enforce mixed-content blocking.
        // For development we'll permit the CDNs explicitly but avoid 'unsafe-inline'.
        $cdnHosts = [
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
            'https://cdnjs.cloudflare.com',
            'https://cdn.jsdelivr.net',
            'https://fonts.googleapis.com'
        ];

        $cdnList = implode(' ', $cdnHosts);

        if (app()->environment('local') || config('app.debug')) {
            // Development: allow inline style attributes (style-src-attr) to avoid refactoring sidebar/button styles.
            // Allow CDNs via element-specific directives.
            $csp = "default-src 'self' https:; "
                . "script-src 'self' 'nonce-$nonce'; "
                . "script-src-elem 'self' 'nonce-$nonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn-script.com https://ajax.googleapis.com; "
                . "style-src 'self' 'nonce-$nonce' https://fonts.googleapis.com; "
                . "style-src-elem 'self' 'nonce-$nonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; "
                . "style-src-attr 'unsafe-inline'; "
                . "img-src 'self' data: blob: https:; "
                . "connect-src 'self' ws: https:; "
                . "font-src 'self' https://fonts.gstatic.com https:; "
                . "block-all-mixed-content; "
                . "frame-ancestors 'self';";
        } else {
            // Production: strict; allow self, trusted Google font origins, and required CDNs, rely on nonce for inline elements
            // Note: style-src-attr 'unsafe-inline' is needed for inline style attributes used in the UI
            $csp = "default-src 'self'; "
                . "script-src 'self' 'nonce-$nonce'; "
                . "script-src-elem 'self' 'nonce-$nonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn-script.com https://ajax.googleapis.com; "
                . "style-src 'self' 'nonce-$nonce' https://fonts.googleapis.com; "
                . "style-src-elem 'self' 'nonce-$nonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; "
                . "style-src-attr 'unsafe-inline'; "
                . "img-src 'self' data:; "
                . "connect-src 'self'; "
                . "font-src 'self' https://fonts.gstatic.com; "
                . "block-all-mixed-content; "
                . "frame-ancestors 'self';";
        }

        // 5. Add CSP header to response
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
