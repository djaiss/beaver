<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\Docs\ApiDocsController;
use App\Http\Controllers\Marketing\Docs\ApiDocsMarkdownController;
use App\Http\Controllers\Marketing\Docs\DocsPortalController;
use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\Marketing\PricingController;
use Illuminate\Support\Facades\Route;

$docsLocales = implode('|', array_keys(config('docs.locales')));

Route::middleware(['marketing'])->group(function () use ($docsLocales): void {
    Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');

    Route::get('pricing', [PricingController::class, 'index'])->name('marketing.pricing.index');

    Route::get('docs/api', [ApiDocsController::class, 'index'])->name('marketing.docs.api.index');
    Route::get('docs/api.md', [ApiDocsMarkdownController::class, 'index'])->name('marketing.docs.api.markdown.index');
    Route::get('docs/api/{section}.md', [ApiDocsMarkdownController::class, 'show'])->where('section', '[a-z0-9\-]+')->name('marketing.docs.api.markdown.show');

    Route::get('docs', [DocsPortalController::class, 'index'])->name('marketing.docs.portal.index');
    Route::get('docs/{locale}', [DocsPortalController::class, 'home'])->where('locale', $docsLocales)->name('marketing.docs.portal.home');
    Route::get('docs/{locale}/{section}/{slug}', [DocsPortalController::class, 'show'])
        ->where('locale', $docsLocales)
        ->where('section', '[a-z0-9\-]+')
        ->where('slug', '[a-z0-9\-]+')
        ->name('marketing.docs.portal.show');
});
