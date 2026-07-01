<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $jsonStructure = [
        'message',
        'status',
        'data' => [
            'token',
        ],
    ];

    #[Test]
    public function it_registers_a_user(): void
    {
        $response = $this->json('POST', '/api/register', [
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'email' => 'chandler.bing@friends.com',
            'password' => '5UTHSmdj',
            'password_confirmation' => '5UTHSmdj',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure($this->jsonStructure);
        $this->assertNotEmpty($response->json('data.token'));

        $this->assertDatabaseHas('users', [
            'email' => 'chandler.bing@friends.com',
        ]);

        $user = User::query()->where('email', 'chandler.bing@friends.com')->first();
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_names_the_token_after_the_device(): void
    {
        $response = $this->json('POST', '/api/register', [
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'email' => 'chandler.bing@friends.com',
            'password' => '5UTHSmdj',
            'password_confirmation' => '5UTHSmdj',
            'device_name' => 'Chandler iPhone 15',
        ]);

        $response->assertCreated();

        $user = User::query()->where('email', 'chandler.bing@friends.com')->first();
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Login from Chandler iPhone 15',
        ]);
    }

    #[Test]
    public function it_requires_a_unique_email(): void
    {
        User::factory()->create([
            'email' => 'chandler.bing@friends.com',
        ]);

        $response = $this->json('POST', '/api/register', [
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'email' => 'chandler.bing@friends.com',
            'password' => '5UTHSmdj',
            'password_confirmation' => '5UTHSmdj',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function it_requires_a_matching_password_confirmation(): void
    {
        $response = $this->json('POST', '/api/register', [
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'email' => 'chandler.bing@friends.com',
            'password' => '5UTHSmdj',
            'password_confirmation' => 'does-not-match',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function it_requires_the_mandatory_fields(): void
    {
        $response = $this->json('POST', '/api/register', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
    }
}
