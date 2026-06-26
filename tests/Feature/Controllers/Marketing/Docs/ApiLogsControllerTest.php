<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiLogsControllerTest extends TestCase
{
    #[Test]
    public function it_renders_the_api_logs_page(): void
    {
        $response = $this->get('/docs/1.x/api/account/logs');

        $response->assertOk();
    }

    #[Test]
    public function it_returns_the_logs_document_as_markdown(): void
    {
        $response = $this->get('/docs/1.x/api/account/logs.md');

        $response->assertOk();
    }
}
