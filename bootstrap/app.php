<?php

declare(strict_types=1);

use App\Http\Middleware\CheckCatalog;
use App\Http\Middleware\CheckCopy;
use App\Http\Middleware\CheckItem;
use App\Http\Middleware\CheckMarketing;
use App\Http\Middleware\EnsureAccountOwner;
use App\Http\Middleware\EnsureEditorAccess;
use App\Http\Middleware\EnsureInstanceAdministrator;
use App\Http\Middleware\EnsureSupportEnabled;
use App\Http\Middleware\HandleOversizedUpload;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetMarketingLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\ResponseCache\Middlewares\CacheResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Runs before CSRF verification: a body over post_max_size arrives without
        // its token, so this must catch it before the token mismatch fires.
        $middleware->web(prepend: [HandleOversizedUpload::class]);

        $middleware->alias([
            'set.locale' => SetLocale::class,
            // the collection domain resolves what the url names, outermost first
            'catalog' => CheckCatalog::class,
            'item' => CheckItem::class,
            'copy' => CheckCopy::class,
            'owner' => EnsureAccountOwner::class,
            'editor' => EnsureEditorAccess::class,
            'instance.admin' => EnsureInstanceAdministrator::class,
            'support.enabled' => EnsureSupportEnabled::class,
            'marketing' => CheckMarketing::class,
            'marketing.locale' => SetMarketingLocale::class,
            'cacheResponse' => CacheResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
