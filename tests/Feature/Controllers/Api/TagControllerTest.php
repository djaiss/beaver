<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the tags of the account', function () {
    $user = $this->createUser();
    Tag::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Signed',
    ]);
    Tag::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'First Issue',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/tags');

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => $this->jsonStructure,
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.attributes.name', 'Signed')
        ->assertJsonPath('data.1.attributes.name', 'First Issue');
});

it('does not list tags from another account', function () {
    $user = $this->createUser();
    Tag::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/tags');

    $response
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('shows a tag', function () {
    $user = $this->createUser();
    $tag = Tag::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Signed',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/tags/'.$tag->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'tag')
        ->assertJsonPath('data.id', (string) $tag->id)
        ->assertJsonPath('data.attributes.name', 'Signed')
        ->assertJsonPath('data.links.self', route('api.tags.show', $tag->id));
});

it('returns not found for a tag from another account', function () {
    $user = $this->createUser();
    $tag = Tag::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/tags/'.$tag->id);

    $response->assertNotFound();
});

it('creates a tag', function () {
    Queue::fake();

    $user = $this->createUser();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/tags', [
        'name' => 'Signed',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Signed');

    $tag = Tag::query()->latest('id')->first();
    expect($tag->name)->toBe('Signed');
    expect($tag->account_id)->toBe($user->account_id);
});

it('validates the name when creating a tag', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/tags', []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('restricts tag creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($viewer);

    $response = $this->json('POST', '/api/tags', [
        'name' => 'Signed',
    ]);

    $response->assertNotFound();
});

it('updates a tag', function () {
    Queue::fake();

    $user = $this->createUser();
    $tag = Tag::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Old name',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/tags/'.$tag->id, [
        'name' => 'Signed',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Signed');

    expect($tag->refresh()->name)->toBe('Signed');
});

it('restricts tag updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $tag = Tag::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('PUT', '/api/tags/'.$tag->id, [
        'name' => 'Signed',
    ]);

    $response->assertNotFound();
});

it('deletes a tag', function () {
    Queue::fake();

    $user = $this->createUser();
    $tag = Tag::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/tags/'.$tag->id);

    $response->assertNoContent();

    $this->assertModelMissing($tag);
});

it('restricts tag deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $tag = Tag::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('DELETE', '/api/tags/'.$tag->id);

    $response->assertNotFound();
});
