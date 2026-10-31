<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax() || $request->is('worklist/*', 'mcu/*')) {
                return response()->json([
                    'message' => 'Sesi keamanan Anda telah berakhir (419 Page Expired). Silakan refresh halaman dan coba lagi.',
                ], 419);
            }
        });
    })->create();
