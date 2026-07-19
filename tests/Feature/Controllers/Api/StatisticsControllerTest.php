<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'totals' => [
                'items',
                'copies',
                'value',
                'average',
                'items_added_this_month',
                'value_added_this_month',
                'undated_copies',
            ],
            'sets_completion',
            'value_over_time',
            'acquisitions_per_month',
            'by_category',
            'by_condition',
            'value_by_location',
            'top_items',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('shows the statistics of a collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Amazing Spider-Man #1']);
    Copy::factory()->create(['item_id' => $item->id, 'estimated_value' => 1000, 'acquired_at' => '2020-01-01']);
    Copy::factory()->create(['item_id' => $item->id, 'estimated_value' => 2000, 'acquired_at' => '2020-02-01']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$collection->id.'/statistics')
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'collection_statistics')
        ->assertJsonPath('data.id', (string) $collection->id)
        ->assertJsonPath('data.attributes.totals.items', 1)
        ->assertJsonPath('data.attributes.totals.copies', 2)
        ->assertJsonPath('data.attributes.totals.value', 3000)
        ->assertJsonPath('data.attributes.totals.average', 3000)
        ->assertJsonPath('data.attributes.sets_completion', null)
        ->assertJsonPath('data.attributes.top_items.0.id', (string) $item->id)
        ->assertJsonPath('data.attributes.top_items.0.name', 'Amazing Spider-Man #1')
        ->assertJsonPath('data.links.self', route('api.collections.statistics', $collection->id));
});

it('shows the sets completion of a collection that has targeted sets', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['collection_id' => $collection->id, 'name' => 'Central Perk', 'target_count' => 4]);
    Item::factory()->create(['collection_id' => $collection->id, 'set_id' => $set->id, 'name' => 'Rachel']);
    Item::factory()->create(['collection_id' => $collection->id, 'set_id' => $set->id, 'name' => 'Monica']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$collection->id.'/statistics')
        ->assertOk()
        ->assertJsonPath('data.attributes.sets_completion.target', 4)
        ->assertJsonPath('data.attributes.sets_completion.owned', 2)
        ->assertJsonPath('data.attributes.sets_completion.remaining', 2)
        ->assertJsonPath('data.attributes.sets_completion.percentage', 50)
        ->assertJsonPath('data.attributes.sets_completion.sets', 1);
});

it('shows empty statistics for a collection without items', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$collection->id.'/statistics')
        ->assertOk()
        ->assertJsonPath('data.attributes.totals.items', 0)
        ->assertJsonPath('data.attributes.totals.copies', 0)
        ->assertJsonPath('data.attributes.totals.value', 0)
        ->assertJsonPath('data.attributes.totals.average', 0);
});

it('returns not found for the statistics of a collection from another account', function () {
    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $collection = Collection::factory()->create(['account_id' => $otherAccount->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$collection->id.'/statistics')
        ->assertNotFound();
});

it('lets a viewer read the statistics of a collection', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    Sanctum::actingAs($viewer);

    $this->json('GET', '/api/collections/'.$collection->id.'/statistics')
        ->assertOk()
        ->assertJsonPath('data.id', (string) $collection->id);
});
