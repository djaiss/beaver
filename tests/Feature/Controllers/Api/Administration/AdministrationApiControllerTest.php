<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->collectionJsonStructure = [
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'token',
                    'last_used_at',
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ],
    ];

    $this->singleJsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'name',
                'token',
                'last_used_at',
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
        'token',
    ];
});

it('can list the api keys of the current user', function () {
    Date::setTestNow('2025-07-01 00:00:00');
    $user = User::factory()->create();

    $user->createToken('Test API Key 1');
    $token2AccessToken = $user->createToken('Test API Key 2')->accessToken;
    $token2AccessToken->last_used_at = Date::now()->subDays(5);
    $token2AccessToken->save();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/api');

    $response->assertJsonStructure($this->collectionJsonStructure);

    $response->assertJsonCount(2, 'data');
});

it('can create a new api key', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/administration/api', [
        'label' => 'New API Key',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('personal_access_tokens', [
        'name' => 'New API Key',
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
    ]);

    $response->assertJsonStructure($this->singleJsonStructure);
});

test('user can delete their api key', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test API Key');
    $tokenId = $token->accessToken->id;

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', "/api/administration/api/{$tokenId}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);
});
