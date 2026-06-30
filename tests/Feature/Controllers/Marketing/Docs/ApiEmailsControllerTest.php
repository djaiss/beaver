<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiEmailsControllerTest extends TestCase
{
    #[Test]
    public function it_renders_the_api_emails_page(): void
    {
        $response = $this->get('/docs/1.x/api/account/emails');

        $response->assertOk();
    }

    #[Test]
    public function it_returns_the_emails_document_as_markdown(): void
    {
        $response = $this->get('/docs/1.x/api/account/emails.md');

        $response->assertOk();
    }
}
