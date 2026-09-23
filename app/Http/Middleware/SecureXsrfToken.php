<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

class SecureXsrfToken
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set XSRF-TOKEN cookie securely
//        Cookie::queue(
//            Cookie::make('XSRF-TOKEN', csrf_token(), 120, '/', null, true, true, false, 'Lax')
//        );

        return $response;
    }
}
