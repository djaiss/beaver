<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsPageControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_docs_index(): void
    {
        $response = $this->get('/docs');

        $response->assertOk();
    }

    #[Test]
    public function it_shows_a_markdown_doc_page(): void
    {
        $response = $this->get('/docs/1.x/organizations/index');

        $response->assertOk();
    }

    #[Test]
    public function it_shows_a_nested_markdown_doc_page(): void
    {
        $response = $this->get('/docs/1.x/branches/manage');

        $response->assertOk();
    }

    #[Test]
    public function it_shows_a_blade_doc_page(): void
    {
        $response = $this->get('/docs/1.x/api/organizations/index');

        $response->assertOk();
    }

    #[Test]
    public function it_returns_404_for_directory_only_path(): void
    {
        $response = $this->get('/docs/1.x/organizations');

        $response->assertNotFound();
    }

    #[Test]
    public function it_returns_404_for_unknown_path(): void
    {
        $response = $this->get('/docs/1.x/nonexistent-page');

        $response->assertNotFound();
    }
}
