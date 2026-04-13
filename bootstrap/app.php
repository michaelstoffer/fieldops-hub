<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/settings.php'));
            Route::middleware('web')
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->validateCsrfTokens(except: ['stripe/webhook', 'health', 'health/ready']);

        $middleware->alias([
            'role'                  => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'            => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'subscription'          => \App\Http\Middleware\CheckSubscription::class,
            'technician.limit'      => \App\Http\Middleware\CheckTechnicianLimit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        \Sentry\Laravel\Integration::handles($exceptions);

        $exceptions->render(function (
            \Spatie\Permission\Exceptions\UnauthorizedException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->header('X-Inertia')) {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have access to that area.');
            }
            return response()->json(['message' => 'Forbidden.'], 403);
        });
    })->create();
