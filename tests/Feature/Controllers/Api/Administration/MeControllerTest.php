<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Administration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_the_information_about_the_logged_user(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create([
                'first_name' => 'Dwight',
                'last_name' => 'Schrute',
                'email' => 'dwight.schrute@dundermifflin.com',
                'nickname' => 'Dwight',
                'locale' => 'en',
                'time_format_24h' => true,
            ]),
        );

        $response = $this->json('GET', '/api/me');

        $response->assertOk();
        $appUrl = config('app.url');

        $this->assertEquals(
            $response->json()['data'],
            [
                'type' => 'user',
                'id' => (string) $user->id,
                'attributes' => [
                    'first_name' => 'Dwight',
                    'last_name' => 'Schrute',
                    'email' => 'dwight.schrute@dundermifflin.com',
                    'nickname' => 'Dwight',
                    'locale' => 'en',
                    'time_format_24h' => true,
                ],
                'links' => [
                    'self' => "{$appUrl}/api/me",
                ],
            ],
        );
    }

    #[Test]
    public function it_updates_the_profile(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
            'email' => 'dwight.schrute@dundermifflin.com',
            'nickname' => 'Dwight',
            'locale' => 'en',
            'time_format_24h' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/me', [
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'email' => 'michael.scott@dundermifflin.com',
            'nickname' => 'Michael',
            'locale' => 'fr_FR',
            'time_format_24h' => 'true',
        ]);

        $response->assertOk();
        $appUrl = config('app.url');

        $this->assertEquals(
            [
                'type' => 'user',
                'id' => (string) $user->id,
                'attributes' => [
                    'first_name' => 'Michael',
                    'last_name' => 'Scott',
                    'email' => 'michael.scott@dundermifflin.com',
                    'nickname' => 'Michael',
                    'locale' => 'fr_FR',
                    'time_format_24h' => true,
                ],
                'links' => [
                    'self' => "{$appUrl}/api/me",
                ],
            ],
            $response->json()['data'],
        );
    }
}
