<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\Docs\ApiDocsController;
use App\Http\Controllers\Marketing\Docs\ApiDocsMarkdownController;
use App\Http\Controllers\Marketing\Docs\DocsPortalController;
use App\Http\Controllers\Marketing\Docs\DocsPortalHomeController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\Marketing\PricingController;
use Illuminate\Support\Facades\Route;

// The short URL prefixes (en, fr, ...), not the locale keys (en, fr_FR, ...):
// the documentation lives right under the domain, so this segment doubles as
// the site wide language prefix once other sections are translated too.
$docsUrlLocales = implode('|', array_column(config('docs.locales'), 'url'));

Route::middleware(['marketing'])->group(function () use ($docsUrlLocales): void {
    Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');

    Route::get('pricing', [PricingController::class, 'index'])->name('marketing.pricing.index');

    Route::get('docs/api', [ApiDocsController::class, 'index'])->name('marketing.docs.api.index');
    Route::get('docs/api.md', [ApiDocsMarkdownController::class, 'index'])->name('marketing.docs.api.markdown.index');
    Route::get('docs/api/{section}.md', [ApiDocsMarkdownController::class, 'show'])->where('section', '[a-z0-9\-]+')->name('marketing.docs.api.markdown.show');

    // The bare /docs URL is kept only as a stable entry point that redirects
    // into the default locale, since the portal itself lives at the root.
    Route::get('docs', [DocsPortalController::class, 'index'])->name('marketing.docs.portal.index');

    Route::get('{locale}', [DocsPortalHomeController::class, 'show'])->where('locale', $docsUrlLocales)->name('marketing.docs.portal.home.show');
    Route::get('{locale}/{section}/{slug}', [DocsPortalController::class, 'show'])
        ->where('locale', $docsUrlLocales)
        ->where('section', '[a-z0-9\-]+')
        ->where('slug', '[a-z0-9\-]+')
        ->name('marketing.docs.portal.show');
});
