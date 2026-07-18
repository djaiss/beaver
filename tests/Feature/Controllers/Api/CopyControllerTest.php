<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * Create an item belonging to the given account.
 */
function itemForAccount(int $accountId): Item
{
    $collection = Collection::factory()->create(['account_id' => $accountId]);

    return Item::factory()->create(['collection_id' => $collection->id]);
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'item_id',
            'condition_id',
            'location_id',
            'acquired_at',
            'price_paid',
            'estimated_value',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the copies of an item', function () {
    $user = $this->createUser();
    $item = itemForAccount($user->account_id);
    Copy::factory()->create(['item_id' => $item->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/copies')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list copies of an item from another account', function () {
    $user = $this->createUser();
    $item = itemForAccount($this->createAccount()->id);
    Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/copies')->assertNotFound();
});

it('shows a copy', function () {
    $user = $this->createUser();
    $item = itemForAccount($user->account_id);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/copies/'.$copy->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'copy')
        ->assertJsonPath('data.id', (string) $copy->id)
        ->assertJsonPath('data.links.self', route('api.items.copies.show', [$item->id, $copy->id]));
});

it('creates a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemForAccount($user->account_id);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/items/'.$item->id.'/copies', [
        'acquired_at' => '2024-01-15',
        'price_paid' => 1200,
        'estimated_value' => 5000,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.attributes.item_id', (string) $item->id)
        ->assertJsonPath('data.attributes.price_paid', 1200);

    $copy = Copy::query()->latest('id')->first();
    expect($copy->item_id)->toBe($item->id);
    expect($copy->price_paid)->toBe(1200);
});

it('validates the price when creating a copy', function () {
    $user = $this->createUser();
    $item = itemForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/copies', ['price_paid' => -1])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['price_paid']);
});

it('restricts copy creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = itemForAccount($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/items/'.$item->id.'/copies', [])->assertNotFound();
});

it('updates a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemForAccount($user->account_id);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'price_paid' => 100]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/items/'.$item->id.'/copies/'.$copy->id, [
        'price_paid' => 9900,
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.price_paid', 9900);

    expect($copy->refresh()->price_paid)->toBe(9900);
});

it('restricts copy updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = itemForAccount($account->id);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/items/'.$item->id.'/copies/'.$copy->id, ['price_paid' => 500])
        ->assertNotFound();
});

it('deletes a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemForAccount($user->account_id);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/items/'.$item->id.'/copies/'.$copy->id)->assertNoContent();

    $this->assertSoftDeleted($copy);
});

it('restricts copy deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = itemForAccount($account->id);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/items/'.$item->id.'/copies/'.$copy->id)->assertNotFound();
});
