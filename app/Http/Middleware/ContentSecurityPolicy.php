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

        // 4. Build strict CSP header
        // All inline scripts/styles MUST use the nonce attribute: nonce="{{ $cspNonce }}"
        
        // Trusted CDN hosts for scripts
        $scriptHosts = [
            "'self'",
            "'nonce-$nonce'",
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
            'https://cdn-script.com',
            'https://ajax.googleapis.com',
            'https://code.jquery.com',
        ];
        
        // Trusted hosts for styles
        $styleHosts = [
            "'self'",
            "'nonce-$nonce'",
            'https://fonts.googleapis.com',
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
        ];
        
        // Trusted hosts for images (no wildcard!)
        $imgHosts = [
            "'self'",
            'data:',
            'blob:',
            'https://cdn.jsdelivr.net',
            'https://nebulastudentportal.slt.lk',
        ];
        
        // Trusted hosts for fonts
        $fontHosts = [
            "'self'",
            'data:',
            'https://fonts.gstatic.com',
            'https://cdn.jsdelivr.net',
        ];
        
        // Trusted hosts for connections (AJAX, fetch, WebSocket)
        $connectHosts = [
            "'self'",
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
        ];

        $scriptList = implode(' ', $scriptHosts);
        $styleList = implode(' ', $styleHosts);
        $imgList = implode(' ', $imgHosts);
        $fontList = implode(' ', $fontHosts);
        $connectList = implode(' ', $connectHosts);

        // Build CSP - NO 'unsafe-inline' anywhere!
        $csp = "default-src 'self'; "
            // Scripts: nonce-based (secure)
            . "script-src $scriptList; "
            . "script-src-elem $scriptList; "
            . "script-src-attr 'none'; "
            // Styles: nonce-based (secure)
            . "style-src $styleList; "
            . "style-src-elem $styleList; "
            . "style-src-attr 'self' 'unsafe-hashes'; "
            // Resources: specific hosts only (no wildcards)
            . "img-src $imgList; "
            . "connect-src $connectList; "
            . "font-src $fontList; "
            . "frame-src 'self' https://nebulastudentportal.slt.lk; "
            . "media-src 'self'; "
            . "manifest-src 'self'; "
            . "worker-src 'self' blob:; "
            . "form-action 'self'; "
            . "object-src 'none'; "
            . "base-uri 'self'; "
            . "frame-ancestors 'self' https://nebulastudentportal.slt.lk; "
            . "upgrade-insecure-requests;";

        // 5. Add CSP header to response
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}

