<?php

declare(strict_types=1);
use App\Enums\CopyStatus;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Valuation;
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
            'identifier',
            'condition_id',
            'current_location_id',
            'status',
            'quantity',
            'disposed_at',
            'note',
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
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'identifier' => 'CP-0042',
        'status' => CopyStatus::Loaned,
        'quantity' => 3,
        'note' => 'Lent to Chandler Bing',
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/copies/'.$copy->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'copy')
        ->assertJsonPath('data.id', (string) $copy->id)
        ->assertJsonPath('data.attributes.identifier', 'CP-0042')
        ->assertJsonPath('data.attributes.status', 'loaned')
        ->assertJsonPath('data.attributes.quantity', 3)
        ->assertJsonPath('data.attributes.note', 'Lent to Chandler Bing')
        ->assertJsonPath('data.links.self', route('api.items.copies.show', [$item->id, $copy->id]));
});

it('shows the estimated value of a copy from its latest valuation', function () {
    $user = $this->createUser();
    $item = itemForAccount($user->account_id);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 1000, 'valued_at' => '2024-01-15']);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 5000, 'valued_at' => '2024-06-15']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/copies/'.$copy->id)
        ->assertOk()
        ->assertJsonPath('data.attributes.estimated_value', 5000);
});

it('shows a null estimated value for a copy that has never been valued', function () {
    $user = $this->createUser();
    $item = itemForAccount($user->account_id);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/copies/'.$copy->id)
        ->assertOk()
        ->assertJsonPath('data.attributes.estimated_value', null);
});

it('creates a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemForAccount($user->account_id);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/items/'.$item->id.'/copies', [
        'identifier' => 'CP-0042',
        'status' => CopyStatus::Loaned->value,
        'quantity' => 2,
        'note' => 'Lent to Joey Tribbiani',
        'estimated_value' => 5000,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.attributes.item_id', (string) $item->id)
        ->assertJsonPath('data.attributes.identifier', 'CP-0042')
        ->assertJsonPath('data.attributes.status', 'loaned')
        ->assertJsonPath('data.attributes.quantity', 2)
        ->assertJsonPath('data.attributes.estimated_value', 5000);

    $copy = Copy::query()->latest('id')->first();
    expect($copy->item_id)->toBe($item->id);
    expect($copy->status)->toBe(CopyStatus::Loaned);
    expect($copy->quantity)->toBe(2);
});

it('creates a copy that is owned by default', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/copies', [])
        ->assertCreated()
        ->assertJsonPath('data.attributes.status', 'owned')
        ->assertJsonPath('data.attributes.quantity', 1)
        ->assertJsonPath('data.attributes.estimated_value', null);
});

it('records the estimated value of a new copy as a valuation', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/copies', ['estimated_value' => 5000])
        ->assertCreated();

    $copy = Copy::query()->latest('id')->first();

    $this->assertDatabaseHas('valuations', [
        'copy_id' => $copy->id,
        'amount' => 5000,
    ]);
});

it('validates the status when creating a copy', function () {
    $user = $this->createUser();
    $item = itemForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/copies', ['status' => 'smelly-cat'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

it('validates the quantity when creating a copy', function () {
    $user = $this->createUser();
    $item = itemForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/copies', ['quantity' => 0])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['quantity']);
});

it('validates the estimated value when creating a copy', function () {
    $user = $this->createUser();
    $item = itemForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/copies', ['estimated_value' => -1])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['estimated_value']);
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
    $copy = Copy::factory()->create(['item_id' => $item->id, 'quantity' => 1]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/items/'.$item->id.'/copies/'.$copy->id, [
        'identifier' => 'CP-0007',
        'status' => CopyStatus::Sold->value,
        'quantity' => 4,
        'disposed_at' => '2024-01-15',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.identifier', 'CP-0007')
        ->assertJsonPath('data.attributes.status', 'sold')
        ->assertJsonPath('data.attributes.quantity', 4);

    $copy->refresh();
    expect($copy->status)->toBe(CopyStatus::Sold);
    expect($copy->quantity)->toBe(4);
    expect($copy->disposed_at->toDateString())->toBe('2024-01-15');
});

it('records a new valuation when the estimated value of a copy moves', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemForAccount($user->account_id);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 1000, 'valued_at' => '2020-01-01']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/items/'.$item->id.'/copies/'.$copy->id, ['estimated_value' => 9900])
        ->assertOk()
        ->assertJsonPath('data.attributes.estimated_value', 9900);

    expect($copy->refresh()->valuations()->count())->toBe(2);
});

it('restricts copy updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = itemForAccount($account->id);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/items/'.$item->id.'/copies/'.$copy->id, ['quantity' => 5])
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
