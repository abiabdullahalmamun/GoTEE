<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    private array $unwantedHeaders = [
        'X-Powered-By',
        'Server',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Remove unwanted headers first
        foreach ($this->unwantedHeaders as $header) {
            $response->headers->remove($header);
        }

        // Core security headers
        $this->applyCoreSecurityHeaders($response);

        // Modern isolation headers
        $this->applyIsolationHeaders($response);

        // Content Security Policy
        $this->applyContentSecurityPolicy($response);

        // Feature Policy (now Permissions-Policy)
        $this->applyPermissionsPolicy($response);

        // Conditional headers
        if (config('app.secure')) {
            $this->applyConditionalHeaders($response);
        }

        return $response;
    }

    protected function applyCoreSecurityHeaders(Response $response): void
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
    }

    protected function applyIsolationHeaders(Response $response): void
    {
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
    }

    protected function applyContentSecurityPolicy(Response $response): void
    {
        $csp = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "script-src-attr 'none'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data:",
            "font-src 'self'",
            "connect-src 'self'",
            "media-src 'self'",
            "worker-src 'self'",
            "manifest-src 'self'",
            "upgrade-insecure-requests",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
    }

    protected function applyPermissionsPolicy(Response $response): void
    {
        $permissions = [
            'accelerometer=()',
            'ambient-light-sensor=()',
            'autoplay=()',
            'camera=()',
            'display-capture=()',
            'document-domain=()',
            'encrypted-media=()',
            'fullscreen=()',
            'geolocation=()',
            'gyroscope=()',
            'magnetometer=()',
            'microphone=()',
            'midi=()',
            'payment=()',
            'picture-in-picture=()',
            'publickey-credentials-get=()',
            'screen-wake-lock=()',
            'sync-xhr=()',
            'usb=()',
            'web-share=()',
            'xr-spatial-tracking=()',
        ];

        $response->headers->set('Permissions-Policy', implode(', ', $permissions));
    }

    protected function applyConditionalHeaders(Response $response): void
    {
        $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        $response->headers->set('Expect-CT', 'max-age=86400, enforce');
    }
}
