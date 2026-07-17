<?php

use App\Support\CrossTenantSecurityLog;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../app/Core/Routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'school' => \App\Http\Middleware\EnsureUserIsAuthenticated::class,
            'tenant' => \App\Http\Middleware\EnsureUserHasSchool::class,
            'module' => \App\Http\Middleware\EnsureModuleEnabled::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            '2fa' => \App\Http\Middleware\EnsureTwoFactorPassed::class,

        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'payment/confirm',
        'payment/validate',
        'api/payment/confirm',
        'api/payment/validate',
        'api/sms/delivery-report',
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (AuthorizationException $e) {
            if (auth()->check()) {
                CrossTenantSecurityLog::authorizationDenied($e);
            }
        });
    })
    ->withProviders([
        App\Providers\FortifyServiceProvider::class,
    ])->create();
