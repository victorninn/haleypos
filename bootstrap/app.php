<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureTenantContext;
use App\Http\Middleware\EnsureSuperadmin;
use App\Http\Middleware\EnsureActiveSubscription;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'            => EnsureUserHasRole::class,
            'tenant'          => EnsureTenantContext::class,
            'superadmin'      => EnsureSuperadmin::class,
            'subscription'    => EnsureActiveSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
