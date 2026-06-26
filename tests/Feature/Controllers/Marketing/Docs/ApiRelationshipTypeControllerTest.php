<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiRelationshipTypeControllerTest extends TestCase
{
    #[Test]
    public function it_renders_the_api_relationship_types_page(): void
    {
        $this->get('/docs/1.x/api/vaults/relationship-types')->assertOk();
    }

    #[Test]
    public function it_returns_the_relationship_types_document_as_markdown(): void
    {
        $this->get('/docs/1.x/api/vaults/relationship-types.md')->assertOk();
    }
}
