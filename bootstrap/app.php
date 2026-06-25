<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
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

        $middleware->redirectTo(
            guests: fn () => route('admin.login'),
            users: fn () => route('admin.dashboard'),
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {

        // Validation error
        if ($e instanceof ValidationException) {
            if (! $request->expectsJson()) {
                return redirect()
                    ->back()
                    ->withErrors($e->errors())
                    ->withInput($request->except('password', 'password_confirmation'));
            }

            return response()->json([
                'data' => [
                    'errors' => $e->errors(),
                ],
                'message' => $e->getMessage(),
            ], $e->status);
        }

        // Database unique constraint fallback
        if ($e instanceof UniqueConstraintViolationException) {
            $exceptionMessage = strtolower($e->getMessage());
            $field = str_contains($exceptionMessage, 'nik') ? 'nik' : 'email';
            $message = $field === 'nik'
                ? 'NIK / NPW sudah digunakan oleh user lain.'
                : 'Email sudah digunakan oleh user lain.';

            if (! $request->expectsJson()) {
                return redirect()
                    ->back()
                    ->withErrors([$field => $message])
                    ->withInput($request->except('password', 'password_confirmation'));
            }

            return response()->json([
                'data' => [
                    'errors' => [
                        $field => [$message],
                    ],
                ],
                'message' => $message,
            ], 422);
        }

        // Authtentication error
        if ($e instanceof AuthenticationException) {
            if (! $request->expectsJson()) {
                return redirect()->guest(route('admin.login'));
            }

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
