<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_docs_index_page(): void
    {
        $response = $this->get('/docs');

        $response->assertOk();
        $response->assertSee('lg:grid-cols-[300px_1fr]', false);
        $response->assertDontSee('lg:grid-cols-[300px_1fr_250px]', false);
    }

    #[Test]
    public function it_renders_a_right_sidebar_when_its_slot_is_provided(): void
    {
        Route::get('/docs-with-right-sidebar', fn (): string => Blade::render(<<<'BLADE'
                <x-marketing-docs-layout>
                    <x-slot:rightSidebar>
                        <p>Right sidebar content</p>
                    </x-slot:rightSidebar>

                    <p>Documentation content</p>
                </x-marketing-docs-layout>
            BLADE))->name('marketing.docs.with-right-sidebar');

        $response = $this->get('/docs-with-right-sidebar');

        $response->assertOk();
        $response->assertSee('lg:grid-cols-[300px_1fr_250px]', false);
        $response->assertSee('Right sidebar content');
    }
}
