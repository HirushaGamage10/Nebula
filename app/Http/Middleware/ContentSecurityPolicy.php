<?php
// app/Http/Middleware/ContentSecurityPolicy.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Generate a unique nonce for this request
        $nonce = base64_encode(random_bytes(16));
        
        // Store nonce in request for use in views
        $request->attributes->set('csp_nonce', $nonce);
        app()->instance('csp_nonce', $nonce);
        
        // Build CSP header
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://ajax.googleapis.com https://code.jquery.com",
            "style-src 'self' 'nonce-{$nonce}' https://cdn.jsdelivr.net https://fonts.googleapis.com",
            "img-src 'self' data: https://cdn.jsdelivr.net",
            "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        return $response;
    }
}