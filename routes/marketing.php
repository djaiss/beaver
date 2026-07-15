<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\Docs\ApiDocsController;
use App\Http\Controllers\Marketing\Docs\ApiDocsMarkdownController;
use App\Http\Controllers\Marketing\MarketingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['marketing'])->group(function (): void {
    Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');

    Route::get('docs', [ApiDocsController::class, 'index'])->name('marketing.docs.index');
    Route::get('docs.md', [ApiDocsMarkdownController::class, 'index'])->name('marketing.docs.markdown.index');
    Route::get('docs/{section}.md', [ApiDocsMarkdownController::class, 'show'])->where('section', '[a-z0-9\-]+')->name('marketing.docs.markdown.show');
});
