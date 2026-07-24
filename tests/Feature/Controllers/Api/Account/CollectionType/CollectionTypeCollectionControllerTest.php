<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\CollectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('sets the collections a type applies to', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $comics = Collection::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $vinyl = Collection::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'collection_ids' => [$comics->id, $vinyl->id],
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.id', (string) $type->id);

    $this->assertDatabaseHas('collection_type', [
        'collection_id' => $comics->id,
        'type_id' => $type->id,
    ]);
    $this->assertDatabaseHas('collection_type', [
        'collection_id' => $vinyl->id,
        'type_id' => $type->id,
    ]);
});

it('ignores collections from another account when syncing', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $foreignCollection = Collection::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'collection_ids' => [$foreignCollection->id],
    ]);

    $response->assertOk();

    $this->assertDatabaseMissing('collection_type', [
        'collection_id' => $foreignCollection->id,
        'type_id' => $type->id,
    ]);
});

it('validates the collection ids when syncing', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'collection_ids' => 'not-an-array',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['collection_ids']);
});

it('returns not found for a type from another account', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'collection_ids' => [],
    ]);

    $response->assertNotFound();
});

it('restricts syncing to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'collection_ids' => [],
    ]);

    $response->assertNotFound();
});
