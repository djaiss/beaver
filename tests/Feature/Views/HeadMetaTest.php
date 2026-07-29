<?php

declare(strict_types=1);

namespace Tests\Feature\Views;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/*
 * partials/meta owns the charset, the viewport and the csrf token, and every layout
 * opens its head with it. A layout that also writes one of them by hand ships the tag
 * twice, which is what the marketing pages did until the duplicates were removed.
 */
final class HeadMetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_the_shared_meta_partial_writes_the_charset_the_viewport_and_the_csrf_token(): void
    {
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

        $this->assertSame([], $offenders);
    }

    public function test_the_shared_meta_partial_uses_self_hosted_fonts(): void
    {
        $contents = (string) file_get_contents(resource_path('views/partials/meta.blade.php'));

        $this->assertStringContainsString('@fonts', $contents);
        $this->assertStringNotContainsString('fonts.bunny.net', $contents);
    }

    public function test_a_marketing_page_renders_each_of_those_tags_exactly_once_and_no_csrf_token(): void
    {
        // The public site runs without a session so its pages can be cached whole, so
        // there is no token to print there. It has no form to protect either.
        config()->set('marketing.show', true);

        $response = $this->get(route('marketing.index', ['locale' => 'en']));

        $response->assertOk();

        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, '<meta charset='));
        $this->assertSame(1, substr_count($html, 'name="viewport"'));
        $this->assertSame(0, substr_count($html, 'name="csrf-token"'));
    }

    public function test_an_application_page_still_renders_each_of_those_tags_exactly_once(): void
    {
        $rachel = $this->createUser();

        // A brand new account is carried on to the getting started screen, which is
        // an application page all the same.
        $response = $this->actingAs($rachel)->followingRedirects()->get(route('dashboard.index'));

        $response->assertOk();

        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, '<meta charset='));
        $this->assertSame(1, substr_count($html, 'name="viewport"'));
        $this->assertSame(1, substr_count($html, 'name="csrf-token"'));
    }
}
