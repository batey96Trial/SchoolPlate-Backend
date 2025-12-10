<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->throttleWithRedis();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ModelNotFoundException $e ) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User Not Found'
                ],
                404
            );
        });
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage() ?: 'Forbidden'
        ], 403);
    });
    $exceptions->render(function (AuthorizationException $e, $request) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage() ?: 'Forbidden'
        ], 403);
    });
    })->create();
