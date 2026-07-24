<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\CatalogType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('sets the collections a type applies to', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $comics = Catalog::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $vinyl = Catalog::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'catalog_ids' => [$comics->id, $vinyl->id],
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.id', (string) $type->id);

    $this->assertDatabaseHas('catalog_type', [
        'catalog_id' => $comics->id,
        'type_id' => $type->id,
    ]);
    $this->assertDatabaseHas('catalog_type', [
        'catalog_id' => $vinyl->id,
        'type_id' => $type->id,
    ]);
});

it('ignores collections from another account when syncing', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $foreignCatalog = Catalog::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'catalog_ids' => [$foreignCatalog->id],
    ]);

    $response->assertOk();

    $this->assertDatabaseMissing('catalog_type', [
        'catalog_id' => $foreignCatalog->id,
        'type_id' => $type->id,
    ]);
});

it('validates the collection ids when syncing', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'catalog_ids' => 'not-an-array',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['catalog_ids']);
});

it('returns not found for a type from another account', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'catalog_ids' => [],
    ]);

    $response->assertNotFound();
});

it('restricts syncing to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CatalogType::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/collections', [
        'catalog_ids' => [],
    ]);

    $response->assertNotFound();
});
