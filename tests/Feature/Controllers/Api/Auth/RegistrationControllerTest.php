<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'message',
        'status',
        'data' => [
            'token',
        ],
    ];
});

it('registers a user', function () {
    $response = $this->json('POST', '/api/register', [
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'email' => 'chandler.bing@friends.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => '5UTHSmdj',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure($this->jsonStructure);
    expect($response->json('data.token'))->not->toBeEmpty();

    $this->assertDatabaseHas('users', [
        'email' => 'chandler.bing@friends.com',
    ]);

    $user = User::query()->where('email', 'chandler.bing@friends.com')->first();
    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});
it('names the token after the device', function () {
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
});
it('requires a unique email', function () {
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
});
it('requires a matching password confirmation', function () {
    $response = $this->json('POST', '/api/register', [
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'email' => 'chandler.bing@friends.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => 'does-not-match',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('password');
});
it('requires the mandatory fields', function () {
    $response = $this->json('POST', '/api/register', []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
});
