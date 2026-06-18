<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiManagementControllerTest extends TestCase
{
    #[Test]
    public function it_renders_the_api_management_page(): void
    {
        $response = $this->get('/docs/1.x/api/account-management/api-management');

        $response->assertOk();
    }

    #[Test]
    public function it_returns_the_api_management_document_as_markdown(): void
    {
        $response = $this->get('/docs/1.x/api/account-management/api-management.md');

        $response->assertOk();
    }
}
