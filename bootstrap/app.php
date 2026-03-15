<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\TraceIdMiddleware::class);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                \Illuminate\Support\Facades\Log::info('Validation failed', [
                    'errors' => $e->errors(),
                    'trace_id' => $request->attributes->get('trace_id')
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                    'meta' => [
                        'trace_id' => $request->attributes->get('trace_id'),
                    ],
                ], 422);
            }
        });

        $exceptions->render(function (\App\Exceptions\ApiException $e, $request) {
            if ($request->is('api/*')) {
                \Illuminate\Support\Facades\Log::warning('API Exception: ' . $e->getMessage(), [
                    'trace_id' => $request->attributes->get('trace_id')
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => null,
                    'meta' => [
                        'trace_id' => $request->attributes->get('trace_id'),
                    ],
                ], $e->getCode() ?: 400);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                    'data' => null,
                    'meta' => [
                        'trace_id' => $request->attributes->get('trace_id'),
                    ],
                ], 404);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This action is unauthorized.',
                    'data' => null,
                    'meta' => [
                        'trace_id' => $request->attributes->get('trace_id'),
                    ],
                ], 403);
            }
        });

        $exceptions->reportable(function (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Unhandled exception: ' . $e->getMessage(), [
                'exception' => $e,
                'trace_id' => request()->attributes->get('trace_id')
            ]);
        });
    })->create();
