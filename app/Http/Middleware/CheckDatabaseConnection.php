<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use PDOException;

class CheckDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        try {
            DB::connection()->getPdo();
        } catch (PDOException $e) {
            logger()->error('Database connection failed (middleware)', [
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Service temporarily unavailable. Database connection failed.'
                ], 503);
            }

            return response()->view('pages.error.db', [], 503);
        }

        return $next($request);
    }
}
