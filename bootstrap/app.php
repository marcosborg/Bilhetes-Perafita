<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$sqlitePath = dirname(__DIR__).'/database/database.sqlite';

if (! file_exists($sqlitePath)) {
    if (! is_dir(dirname($sqlitePath))) {
        mkdir(dirname($sqlitePath), 0755, true);
    }

    touch($sqlitePath);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
