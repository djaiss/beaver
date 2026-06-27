<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_api_profile_page(): void
    {
        $response = $this->get('/docs/1.x/api/account/profile');

        $response->assertOk();
    }

    #[Test]
    public function it_returns_the_profile_document_as_markdown(): void
    {
        $response = $this->get('/docs/1.x/api/account/profile.md');

        $response->assertOk();
    }
}
