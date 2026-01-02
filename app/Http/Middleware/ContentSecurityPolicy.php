<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class ContentSecurityPolicy
{
    public function handle($request, Closure $next)
    {
        // 1. Use session-based nonce for consistency across requests
        // This prevents CSP violations when pages are cached
        if (!session()->has('csp_nonce')) {
            session(['csp_nonce' => base64_encode(random_bytes(16))]);
        }
        $nonce = session('csp_nonce');

        // 2. Share it with all Blade views
        View::share('cspNonce', $nonce);

        // 3. Continue the request
        $response = $next($request);

        // 4. Build CSP header
        // Uses nonces for inline <script> and <style> blocks
        // Allows inline attributes for UI framework compatibility
        
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
        
        // Trusted hosts for images
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
            'https://cdnjs.cloudflare.com',
        ];
        
        // Trusted hosts for connections (AJAX, fetch, WebSocket)
        $connectHosts = [
            "'self'",
            'https://nebulastudentportal.slt.lk',
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
            'https://cdn.tailwindcss.com',
        ];

        $scriptList = implode(' ', $scriptHosts);
        $styleList = implode(' ', $styleHosts);
        $imgList = implode(' ', $imgHosts);
        $fontList = implode(' ', $fontHosts);
        $connectList = implode(' ', $connectHosts);

        // Build CSP
        // Note: 'unsafe-inline' in script-src-attr and style-src-attr is required 
        // for Bootstrap and other UI frameworks that use inline event handlers and styles
        $csp = "default-src 'self'; "
            // Scripts: nonce-based for <script> blocks, allow inline event handlers
            . "script-src $scriptList; "
            . "script-src-elem $scriptList; "
            . "script-src-attr 'unsafe-inline'; "
            // Styles: nonce-based for <style> blocks, allow inline style attributes
            . "style-src $styleList; "
            . "style-src-elem $styleList; "
            . "style-src-attr 'unsafe-inline'; "
            // Resources
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
            . "frame-ancestors 'self' https://nebulastudentportal.slt.lk;";

        // 5. Add CSP header to response
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
