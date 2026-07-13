<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_the_account_page(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->get('/settings/user');

        $response->assertOk();
        $response->assertViewIs('app.settings.user.index');
    }

    #[Test]
    public function it_deletes_the_account(): void
    {
        Queue::fake();
        Mail::fake();

        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->delete('/settings/user', [
                'feedback' => 'I no longer need this service',
            ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
