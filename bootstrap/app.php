<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\SecureXsrfToken;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// use Illuminate\Database\QueryException;
// use PDOException;
// use Symfony\Component\HttpFoundation\Response;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // 👈 Added this
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => CheckPermission::class,
            'securityHeaders' => SecurityHeaders::class,
//            'xrf'=>SecureXsrfToken::class,
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // 👈 Added this
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
