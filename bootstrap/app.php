<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        // Registrar middleware CORS personalizado
        $middleware->alias([
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
        ]);

        // Registrar middleware CORS globalmente
        $middleware->append(\App\Http\Middleware\CorsMiddleware::class);

        $middleware->validateCsrfTokens(except: [
            'log'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {})->create();
