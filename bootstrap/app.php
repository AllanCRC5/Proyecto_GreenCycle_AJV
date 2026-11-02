<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Las rutas de la API deben responder en formato JSON.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Error 404: acceso denegado por una política que oculta el recurso.
        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if ($request->is('api/*') && $exception->hasStatus(404)) {
                return response()->json([
                    'code' => 'NOT_FOUND',
                    'message' => 'El recurso solicitado no existe.',
                ], 404);
            }
        });

        // Error 401: usuario sin autenticación.
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => 'No estás autenticado.',
                ], 401);
            }
        });

        // Error 404: ruta o recurso inexistente.
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'code' => 'NOT_FOUND',
                    'message' => 'El recurso solicitado no existe.',
                ], 404);
            }
        });
    })->create();
