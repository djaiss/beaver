<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiPersonManagementControllerTest extends TestCase
{
    #[Test]
    public function it_renders_the_api_person_management_page(): void
    {
        $response = $this->get('/docs/1.x/api/persons/person-management');

        $response->assertOk();
    }

    #[Test]
    public function it_returns_the_person_management_document_as_markdown(): void
    {
        $response = $this->get('/docs/1.x/api/persons/person-management.md');

        $response->assertOk();
    }
}
