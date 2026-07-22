<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\Docs\ApiDocsController;
use App\Http\Controllers\Marketing\Docs\ApiDocsMarkdownController;
use App\Http\Controllers\Marketing\Docs\DocsPortalController;
use App\Http\Controllers\Marketing\Docs\DocsPortalHomeController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\Marketing\PricingController;
use Illuminate\Support\Facades\Route;

// The whole public site lives behind a language prefix (getkollek.com/en/...),
// so the URL reads naturally in the reader's language end to end. The prefix is
// the short form (en, fr), and is constrained to the locales that actually have
// content on disk, mirroring DocumentationPortal::availableLocales().
$portalPath = config('docs.portal_path');
$urlLocales = collect(config('docs.locales'))
    ->filter(fn (array $meta, string $locale): bool => is_dir($portalPath.DIRECTORY_SEPARATOR.$locale))
    ->pluck('url')
    ->implode('|');

Route::middleware(['marketing'])->group(function () use ($urlLocales): void {
    // Stable, unprefixed entry points that redirect into the default locale, so
    // getkollek.com and getkollek.com/docs keep working as canonical shortcuts.
    Route::get('/', fn () => redirect()->route('marketing.index'))->name('marketing.root');
    Route::get('docs', [DocsPortalController::class, 'index'])->name('marketing.docs.portal.index');

    Route::prefix('{locale}')->where(['locale' => $urlLocales])->middleware('marketing.locale')->group(function (): void {
        Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');

        Route::get('pricing', [PricingController::class, 'index'])->name('marketing.pricing.index');

        Route::get('docs/api', [ApiDocsController::class, 'index'])->name('marketing.docs.api.index');
        Route::get('docs/api.md', [ApiDocsMarkdownController::class, 'index'])->name('marketing.docs.api.markdown.index');
        Route::get('docs/api/{section}.md', [ApiDocsMarkdownController::class, 'show'])->where('section', '[a-z0-9\-]+')->name('marketing.docs.api.markdown.show');

        Route::get('docs', [DocsPortalHomeController::class, 'show'])->name('marketing.docs.portal.home.show');
        Route::get('docs/{section}/{slug}', [DocsPortalController::class, 'show'])
            ->where('section', '[a-z0-9\-]+')
            ->where('slug', '[a-z0-9\-]+')
            ->name('marketing.docs.portal.show');
    });
});
