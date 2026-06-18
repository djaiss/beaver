<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiAuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_api_authentication_page(): void
    {
        $response = $this->get('/docs/1.x/api/authentication');

        $response->assertOk();
    }
}
