<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->PreventRequestForgery(except: [
            '/api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {

        // Authtentication error
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'data' => [],
                'message' => 'Failed to get data! User unauthenticated.'
            ], 401);
        }

        // HTTP Exception error (404, 403, dll)
        if ($e instanceof HttpExceptionInterface) {
            return response()->json([
                'data' => [],
                'message' => $e->getMessage() ?: 'Error'
            ], $e->getStatusCode());
        }

        // Internal server error (500)
        return response()->json([
            'data' => [],
            'message' => 'Internal Server Error'
        ], 500);
    });
    })->create();
