<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'edition' => \App\Http\Middleware\EditionMiddleware::class,
            'limits' => \App\Http\Middleware\EnforceLicenseLimits::class,
            'subscription.feature' => \App\Http\Middleware\SubscriptionFeatureMiddleware::class,
            'feature.access' => \App\Http\Middleware\CheckFeatureAccess::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\InstallationMiddleware::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\EnsureActiveBranch::class,
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $middleware->appendToGroup('api', [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->wantsJson(),
        );

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Resource not found.'], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            \Illuminate\Support\Facades\Log::warning('Authorization failure: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'This action is unauthorized.'], 403);
            }
        });
        $exceptions->report(function (\Throwable $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                $e instanceof \Illuminate\Auth\AuthenticationException) {
                return;
            }

            \Illuminate\Support\Facades\Log::channel('critical')->critical('CRITICAL_EXCEPTION: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 1000),
            ]);
        });
    })->create();
