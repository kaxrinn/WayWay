<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // alias middleware role
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // redirect guest (BELUM LOGIN) ke route wisatawan.login
        $middleware->redirectGuestsTo(fn () => route('wisatawan.login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
