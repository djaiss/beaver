<?php

declare(strict_types=1);

use App\Http\Middleware\CheckMarketing;
use App\Http\Middleware\EnsureAccountOwner;
use App\Http\Middleware\EnsureEditorAccess;
use App\Http\Middleware\EnsureInstanceAdministrator;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetMarketingLocale;
use Illuminate\Foundation\Application;
use Spatie\ResponseCache\Middlewares\CacheResponse;
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
            'owner' => EnsureAccountOwner::class,
            'editor' => EnsureEditorAccess::class,
            'instance.admin' => EnsureInstanceAdministrator::class,
            'marketing' => CheckMarketing::class,
            'marketing.locale' => SetMarketingLocale::class,
            'cacheResponse' => CacheResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
