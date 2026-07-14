<?php

declare(strict_types=1);
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'user_name',
            'action',
            'parameters',
            'description',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the logs of the current user', function () {
    Date::setTestNow('2025-06-30 12:00:00');
    $user = User::factory()->create([
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
    ]);
    $log = Log::factory()->create([
        'user_id' => $user->id,
        'user_name' => 'Chandler Bing',
        'action' => 'magic_link_created',
        'parameters' => null,
    ]);

    $anotherUser = User::factory()->create();
    $anotherLog = Log::factory()->create([
        'user_id' => $anotherUser->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/logs');

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => $this->jsonStructure,
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.id', (string) $log->id)
        ->assertJsonPath('data.0.attributes.user_name', 'Chandler Bing')
        ->assertJsonPath('data.0.attributes.action', 'magic_link_created')
        ->assertJsonPath('data.0.attributes.description', 'Sent a magic link')
        ->assertJsonPath('data.0.attributes.created_at', 1751284800)
        ->assertJsonMissing(['id' => (string) $anotherLog->id]);
});

it('paginates the logs', function () {
    $user = User::factory()->create();

    Log::factory()->count(15)->create([
        'user_id' => $user->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/logs');

    $response
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 15);
});

it('shows a log', function () {
    Date::setTestNow('2025-06-30 12:00:00');
    $user = User::factory()->create([
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
    ]);
    $log = Log::factory()->create([
        'user_id' => $user->id,
        'user_name' => 'Chandler Bing',
        'action' => 'user_profile_updated',
        'parameters' => null,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/logs/'.$log->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'log')
        ->assertJsonPath('data.id', (string) $log->id)
        ->assertJsonPath('data.attributes.user_name', 'Chandler Bing')
        ->assertJsonPath('data.attributes.action', 'user_profile_updated')
        ->assertJsonPath('data.attributes.parameters', null)
        ->assertJsonPath('data.attributes.description', 'Updated their personal profile')
        ->assertJsonPath('data.attributes.created_at', 1751284800)
        ->assertJsonPath('data.attributes.updated_at', 1751284800)
        ->assertJsonPath('data.links.self', route('api.administration.logs.show', $log));
});

it('cannot show another users log', function () {
    $user = User::factory()->create();
    $anotherLog = Log::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/logs/'.$anotherLog->id);

    $response->assertNotFound();
});
