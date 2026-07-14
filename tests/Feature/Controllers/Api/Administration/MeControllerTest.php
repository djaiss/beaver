<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('returns the information about the logged user', function () {
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

    $response->assertStatus(200);

    expect([
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
            'self' => config('app.url').'/api/me',
        ],
    ])->toEqual($response->json()['data']);
});

it('updates the profile', function () {
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

    $response->assertStatus(200);

    expect($response->json()['data'])->toEqual([
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
            'self' => config('app.url').'/api/me',
        ],
    ]);
});

it('updates the profile when no nickname is provided', function () {
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
        'locale' => 'fr_FR',
        'time_format_24h' => 'true',
    ]);

    $response->assertStatus(200);
    expect($response->json()['data']['attributes']['nickname'])->toBeNull();
});
