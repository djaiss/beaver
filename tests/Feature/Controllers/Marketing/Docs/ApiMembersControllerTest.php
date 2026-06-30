<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiMembersControllerTest extends TestCase
{
    #[Test]
    public function it_renders_the_api_members_page(): void
    {
        $response = $this->get('/docs/1.x/api/vaults/members');

        $response->assertOk();
    }

    #[Test]
    public function it_returns_the_members_document_as_markdown(): void
    {
        $response = $this->get('/docs/1.x/api/vaults/members.md');

        $response->assertOk();
    }
}
