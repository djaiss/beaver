<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\DocumentationPortal;
use Illuminate\Console\Command;

class CacheDocumentationCommand extends Command
{
    protected $signature = 'docs:cache';

    protected $description = 'Build and cache the documentation portal index for production.';

    public function handle(DocumentationPortal $portal): int
    {
        $portal->refreshCache();

        $locales = $portal->availableLocales();
        $pages = collect($locales)->sum(fn (string $locale): int => count($portal->pagesFor($locale)));

        $this->info(sprintf(
            'Cached the documentation index: %d page(s) across %d locale(s) (%s).',
            $pages,
            count($locales),
            implode(', ', $locales),
        ));

        return self::SUCCESS;
    }
}
