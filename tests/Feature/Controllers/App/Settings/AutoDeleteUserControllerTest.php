<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AutoDeleteUserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_enables_auto_delete_user(): void
    {
        $user = $this->createUser([
            'auto_delete_user' => false,
        ]);

        $response = $this->actingAs($user)
            ->from('/settings/security')
            ->put('/settings/security/auto-delete-account', [
                'auto_delete_user' => 'yes',
            ]);

        $response->assertRedirect('/settings/security');
    }
}
