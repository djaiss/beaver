<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'object_type',
            'name',
            'subtitle',
            'deleted_at',
            'deleted_by_name',
            'days_left',
        ],
    ];
});

it('lists the trashed objects of the account', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #1']);
    $item->delete();
    $catalog->delete();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/trash')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
        ])
        ->assertJsonPath('data.0.type', 'trashed_object');

    $objectTypes = collect($response->json('data'))->pluck('attributes.object_type')->all();
    expect($objectTypes)->toContain('collection', 'item');
});

it('does not list the trashed objects of another account', function () {
    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $catalog = Catalog::factory()->create(['account_id' => $otherAccount->id]);
    $catalog->delete();

    Sanctum::actingAs($user);

    $this->json('GET', '/api/trash')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('restores an object from the trash', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #1']);
    $item->delete();

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/trash', [
        'type' => 'item',
        'id' => $item->id,
    ])
        ->assertNoContent();

    expect($item->refresh()->deleted_at)->toBeNull();
});

it('validates the type and the id when restoring an object', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/trash', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type', 'id']);
});

it('rejects an unknown type when restoring an object', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/trash', [
        'type' => 'phoebe',
        'id' => 1,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

it('returns not found when restoring an object of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $catalog = Catalog::factory()->create(['account_id' => $otherAccount->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $item->delete();

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/trash', [
        'type' => 'item',
        'id' => $item->id,
    ])
        ->assertNotFound();

    expect($item->refresh()->deleted_at)->not->toBeNull();
});

it('restricts restoring an object to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $item->delete();

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/trash', [
        'type' => 'item',
        'id' => $item->id,
    ])
        ->assertNotFound();

    expect($item->refresh()->deleted_at)->not->toBeNull();
});

it('empties the trash', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $item->delete();

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/trash')
        ->assertNoContent();

    expect(Item::withTrashed()->whereKey($item->id)->exists())->toBeFalse();
});

it('does not empty the trash of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $catalog = Catalog::factory()->create(['account_id' => $otherAccount->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $item->delete();

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/trash')
        ->assertNoContent();

    expect(Item::withTrashed()->whereKey($item->id)->exists())->toBeTrue();
});

it('restricts emptying the trash to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $item->delete();

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/trash')
        ->assertNotFound();

    expect(Item::withTrashed()->whereKey($item->id)->exists())->toBeTrue();
});
