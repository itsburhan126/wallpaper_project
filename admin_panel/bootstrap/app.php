<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
             \CodeLab\LicenseSystem\Http\Middleware\CheckLicense::class,
        ]);

        $middleware->api(append: [
            // \CodeLab\LicenseSystem\Http\Middleware\CheckLicense::class,
            // \App\Http\Middleware\UpdateLastSeen::class, // Moved to route middleware
        ]);

        $middleware->alias([
            'admin.permission' => \App\Http\Middleware\CheckAdminPermission::class,
            'admin.demo' => \App\Http\Middleware\CheckDemoMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
