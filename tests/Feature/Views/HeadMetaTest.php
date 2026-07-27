<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Finder\Finder;

uses(RefreshDatabase::class);

/*
 * partials/meta owns the charset, the viewport and the csrf token, and every layout
 * opens its head with it. A layout that also writes one of them by hand ships the tag
 * twice, which is what the marketing pages did until the duplicates were removed.
 */
test('only the shared meta partial writes the charset, the viewport and the csrf token', function () {
    $owned = '/<meta\s+(charset=|name="viewport"|name="csrf-token")/';

    $offenders = [];

    foreach (Finder::create()->files()->in(resource_path('views'))->name('*.blade.php') as $file) {
        if ($file->getRelativePathname() === 'partials/meta.blade.php') {
            continue;
        }

        $contents = (string) file_get_contents($file->getRealPath());

        if (! str_contains($contents, "@include('partials.meta'")) {
            continue;
        }

        if (preg_match($owned, $contents) !== 1) {
            continue;
        }

        $offenders[] = $file->getRelativePathname();
    }

    expect($offenders)->toBe([]);
});

test('a marketing page renders each of those tags exactly once', function () {
    config()->set('marketing.show', true);

    $response = $this->get(route('marketing.index', ['locale' => 'en']));

    $response->assertOk();

    $html = $response->getContent();

    expect(substr_count($html, '<meta charset='))->toBe(1)
        ->and(substr_count($html, 'name="viewport"'))->toBe(1)
        ->and(substr_count($html, 'name="csrf-token"'))->toBe(1);
});
