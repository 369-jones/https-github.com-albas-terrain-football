<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(self), camera=(), microphone=()');

        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set(
                'Content-Security-Policy',
                // 'unsafe-inline' on script-src is needed because the club back-office layout
                // (layouts/main.blade.php) has inline <script> blocks for notification polling —
                // tightening this would mean moving that to an external file with a nonce.
                // style-src/font-src allow Google Fonts, loaded by the marketplace layout.
                "default-src 'self'; ".
                "script-src 'self' 'unsafe-inline'; ".
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; ".
                "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; ".
                "img-src 'self' data: https:; ".
                "frame-ancestors 'self';"
            );
        }

        return $response;
    }
}
