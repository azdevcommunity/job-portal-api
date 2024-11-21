<?php

use App\Providers\AuthServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withProviders([
        AuthServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e) {
            if ($e instanceof AuthenticationException) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if ($e instanceof Error) {
                return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
            }

            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            return response()->json(['message' => $e->getMessage()], $statusCode);
        });
    })->create();
