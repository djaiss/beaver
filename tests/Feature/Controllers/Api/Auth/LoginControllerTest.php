<?php

declare(strict_types=1);
use App\Enums\EmailType;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Mail\NewLoginDetected;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use PragmaRX\Google2FALaravel\Google2FA;

uses(RefreshDatabase::class);

it('logs in a user', function () {
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
    expect($responseData['data']['token'])->not->toBeEmpty();
});
it('logs the token creation and notifies of the new login', function () {
    Queue::fake();

    $user = User::factory()->create([
        'email' => 'rachel.green@friends.com',
        'password' => bcrypt('password'),
    ]);

    $this->json('POST', '/api/login', [
        'email' => 'rachel.green@friends.com',
        'password' => 'password',
        'device_name' => 'Rachel iPhone 15',
    ])->assertStatus(200);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::ApiKeyCreation
            && $job->user->id === $user->id
        ),
    );

    Queue::assertPushedOn(
        queue: 'high',
        job: SendEmail::class,
        callback: fn (SendEmail $job): bool => (
            $job->mailable instanceof NewLoginDetected
            && $job->mailable->device === 'Rachel iPhone 15'
            && $job->user->id === $user->id
            && $job->emailType === EmailType::NewLogin
        ),
    );
});
it('labels the new login notification for an unknown device', function () {
    Queue::fake();

    User::factory()->create([
        'email' => 'rachel.green@friends.com',
        'password' => bcrypt('password'),
    ]);

    $this->json('POST', '/api/login', [
        'email' => 'rachel.green@friends.com',
        'password' => 'password',
    ])->assertStatus(200);

    Queue::assertPushed(
        SendEmail::class,
        fn (SendEmail $job): bool => (
            $job->mailable instanceof NewLoginDetected
            && $job->mailable->device === 'an unknown device'
        ),
    );
});
it('names the token after the device', function () {
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
});
it('names the token when no device is provided', function () {
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
});
it('rejects invalid credentials', function () {
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
});
it('requires a 2fa code when two factor is enabled', function () {
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
});
it('rejects an invalid 2fa code', function () {
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
});
it('issues a token with a valid 2fa code', function () {
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
    expect($response->json('data.token'))->not->toBeEmpty();
});
it('authenticates with a recovery code', function () {
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
    expect($user->two_factor_recovery_codes)->not->toContain('ABC123');
});
it('logs out a user', function () {
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
    expect($responseData['message'])->toEqual('Logged out successfully');
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});
