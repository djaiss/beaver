<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\Docs\DocsIndexController;
use App\Http\Controllers\Marketing\Docs\DocsMarkdownController;
use App\Http\Controllers\Marketing\Docs\DocsPageController;
use App\Http\Controllers\Marketing\MarketingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['marketing'])->group(function (): void {
    Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');

    Route::get('/docs', [DocsIndexController::class, 'index'])->name('marketing.docs.index');

    Route::get('/docs/{version}/{path}.md', [DocsMarkdownController::class, 'show'])
        ->where([
            'version' => implode('|', array_map(preg_quote(...), config('docs.versions'))),
            'path' => '.*',
        ])
        ->name('marketing.docs.markdown.show');

    Route::get('/docs/{version}/{path?}', [DocsPageController::class, 'show'])
        ->where([
            'version' => implode('|', array_map(preg_quote(...), config('docs.versions'))),
            'path' => '.*',
        ])
        ->name('marketing.docs.show');
});
