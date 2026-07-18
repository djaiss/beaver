<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\Docs\ApiDocsController;
use App\Http\Controllers\Marketing\Docs\ApiDocsMarkdownController;
use App\Http\Controllers\Marketing\MarketingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['marketing'])->group(function (): void {
    Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');

    Route::get('docs/api', [ApiDocsController::class, 'index'])->name('marketing.docs.api.index');
    Route::get('docs/api.md', [ApiDocsMarkdownController::class, 'index'])->name('marketing.docs.api.markdown.index');
    Route::get('docs/api/{section}.md', [ApiDocsMarkdownController::class, 'show'])->where('section', '[a-z0-9\-]+')->name('marketing.docs.api.markdown.show');
});
