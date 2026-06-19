<?php

declare(strict_types=1);

use App\Http\Middleware\CheckAdminland;
use App\Http\Middleware\CheckMarketing;
use App\Http\Middleware\CheckPerson;
use App\Http\Middleware\CheckVault;
use App\Http\Middleware\CheckVaultAPI;
use App\Http\Middleware\SetLocale;
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
        $middleware->alias([
            'set.locale' => SetLocale::class,
            'vault' => CheckVault::class,
            'marketing' => CheckMarketing::class,
            'vault.api' => CheckVaultAPI::class,
            'vault.adminland' => CheckAdminland::class,
            'person' => CheckPerson::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
