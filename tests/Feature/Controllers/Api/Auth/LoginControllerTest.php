<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use PragmaRX\Google2FALaravel\Google2FA;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_in_a_user(): void
    {
        User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'rachel.green@friends.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'token',
            ],
        ]);

        $responseData = $response->json();
        $this->assertNotEmpty($responseData['data']['token']);
    }

    #[Test]
    public function it_names_the_token_after_the_device(): void
    {
        $user = User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'rachel.green@friends.com',
            'password' => 'password',
            'device_name' => 'Rachel iPhone 15',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Login from Rachel iPhone 15',
        ]);
    }

    #[Test]
    public function it_names_the_token_when_no_device_is_provided(): void
    {
        $user = User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'rachel.green@friends.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Login from an unknown device',
        ]);
    }

    #[Test]
    public function it_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'rachel.green@friends.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function it_requires_a_2fa_code_when_two_factor_is_enabled(): void
    {
        $google2fa = new Google2FA(request());
        $secret = $google2fa->generateSecretKey();

        User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'rachel.green@friends.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function it_rejects_an_invalid_2fa_code(): void
    {
        $google2fa = new Google2FA(request());
        $secret = $google2fa->generateSecretKey();

        User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'rachel.green@friends.com',
            'password' => 'password',
            'code' => '000000',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function it_issues_a_token_with_a_valid_2fa_code(): void
    {
        $google2fa = new Google2FA(request());
        $secret = $google2fa->generateSecretKey();

        User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'rachel.green@friends.com',
            'password' => 'password',
            'code' => $google2fa->getCurrentOtp($secret),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'status',
            'data' => ['token'],
        ]);
        $this->assertNotEmpty($response->json('data.token'));
    }

    #[Test]
    public function it_authenticates_with_a_recovery_code(): void
    {
        $google2fa = new Google2FA(request());
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => ['ABC123', 'DEF456'],
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'rachel.green@friends.com',
            'password' => 'password',
            'code' => 'ABC123',
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertNotContains('ABC123', $user->two_factor_recovery_codes);
    }

    #[Test]
    public function it_logs_out_a_user(): void
    {
        $user = User::factory()->create([
            'email' => 'rachel.green@friends.com',
            'password' => bcrypt('password'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', '/api/logout');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'status',
        ]);

        $responseData = $response->json();
        $this->assertEquals('Logged out successfully', $responseData['message']);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
