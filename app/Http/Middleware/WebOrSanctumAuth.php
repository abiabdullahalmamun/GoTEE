<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebOrSanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated via web or sanctum
        if (Auth::guard('web')->check() || Auth::guard('sanctum')->check()) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
