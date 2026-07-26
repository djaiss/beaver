<?php

declare(strict_types=1);
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'currency',
            'totals' => [
                'collections',
                'items',
                'copies',
                'valued_copies',
                'value',
                'average',
                'items_added_this_month',
                'value_added_this_month',
            ],
            'collections',
            'recent_additions',
            'loans' => [
                'outgoing',
                'incoming',
                'overdue',
                'dueSoon',
                'planned',
                'returned',
                'deposits',
            ],
            'value_by_location',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('shows the dashboard of the account', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #1']);
    $location = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Display Case']);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'current_location_id' => $location->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 3000]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/dashboard')
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'account_dashboard')
        ->assertJsonPath('data.id', (string) $user->account_id)
        ->assertJsonPath('data.attributes.totals.collections', 1)
        ->assertJsonPath('data.attributes.totals.items', 1)
        ->assertJsonPath('data.attributes.totals.copies', 1)
        ->assertJsonPath('data.attributes.totals.valued_copies', 1)
        ->assertJsonPath('data.attributes.totals.value', 3000)
        ->assertJsonPath('data.attributes.collections.0.collection_name', 'Comics')
        ->assertJsonPath('data.attributes.recent_additions.0.name', 'Amazing Spider-Man #1')
        ->assertJsonPath('data.attributes.value_by_location.0.label', 'Display Case');
});

it('does not count another account', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['account_id' => $user->account_id]);

    $stranger = $this->createUser();
    $theirs = Catalog::factory()->create(['account_id' => $stranger->account_id]);
    $item = Item::factory()->create(['catalog_id' => $theirs->id]);
    Valuation::factory()->create(['copy_id' => Copy::factory()->create(['item_id' => $item->id])->id, 'amount' => 90000]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/dashboard')
        ->assertOk()
        ->assertJsonPath('data.attributes.totals.collections', 1)
        ->assertJsonPath('data.attributes.totals.items', 0)
        ->assertJsonPath('data.attributes.totals.value', 0);
});

it('does not let a stranger read the dashboard', function () {
    $this->json('GET', '/api/dashboard')->assertUnauthorized();
});
