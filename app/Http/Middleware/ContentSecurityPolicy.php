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

        // 4. Set CSP header with nonces for inline scripts/styles
        // This configuration:
        // - Uses nonces for inline <script> and <style> blocks (secure, fixes ZAP issues)
        // - Allows inline style attributes for UI framework compatibility
        // - Allows trusted CDN hosts for external resources
        
        $cdnHosts = [
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
            'https://cdn-script.com',
            'https://ajax.googleapis.com',
            'https://code.jquery.com',
        ];
        $cdnList = implode(' ', $cdnHosts);

        // Style sources - include Google Fonts
        $styleSources = [
            "'self'",
            "'nonce-$nonce'",
            'https://fonts.googleapis.com',
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
        ];
        $styleList = implode(' ', $styleSources);

        $csp = "default-src 'self'; "
            // Scripts: use nonces, no unsafe-inline (fixes ZAP script-src issue)
            . "script-src 'self' 'nonce-$nonce' $cdnList; "
            . "script-src-elem 'self' 'nonce-$nonce' $cdnList; "
            . "script-src-attr 'self' 'unsafe-hashes'; "
            // Styles: use nonces for <style> blocks, allow inline style attributes
            . "style-src $styleList; "
            . "style-src-elem $styleList; "
            . "style-src-attr 'self' 'unsafe-inline'; "
            // Resources
            . "img-src 'self' data: blob: https:; "
            . "connect-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com; "
            . "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net; "
            . "frame-src 'self' https://nebulastudentportal.slt.lk; "
            . "media-src 'self'; "
            . "manifest-src 'self'; "
            . "worker-src 'self' blob:; "
            . "form-action 'self'; "
            . "object-src 'none'; "
            . "base-uri 'self'; "
            . "frame-ancestors 'self' https://nebulastudentportal.slt.lk;";

        // 5. Add CSP header to response
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
