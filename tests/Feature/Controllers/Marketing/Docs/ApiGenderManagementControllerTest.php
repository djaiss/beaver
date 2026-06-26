<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiGenderManagementControllerTest extends TestCase
{
    #[Test]
    public function it_renders_the_api_gender_management_page(): void
    {
        $response = $this->get('/docs/1.x/api/vaults/gender-management');

        $response->assertOk();
    }

    #[Test]
    public function it_returns_the_gender_management_document_as_markdown(): void
    {
        $response = $this->get('/docs/1.x/api/vaults/gender-management.md');

        $response->assertOk();
    }
}
