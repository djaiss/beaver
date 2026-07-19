<?php

declare(strict_types=1);
use App\Enums\CopyStatus;
use App\Enums\PermissionEnum;
use App\Enums\ValuationType;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the history of an item', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'identifier' => 'CENTRAL-PERK-01',
        'status' => CopyStatus::Loaned,
    ]);

    $response = $this->actingAs($user)->get(route('items.history.index', [$collection, $item]));

    $response->assertOk()
        ->assertSee('data-test="history-copy-'.$copy->id.'"', false)
        ->assertSee('CENTRAL-PERK-01')
        ->assertSee('Loaned out');
});

it('marks the history tab as the current page', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="item-tab-history"', false)
        ->assertSee('aria-current="page"', false);
});

// The sections are listed rather than hidden until they are built, so the shape
// of the screen says what the history will be assembled from.
it('lists the sections the history is assembled from', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSeeInOrder([
            'Timeline',
            'Transactions',
            'Provenance',
            'Valuations',
            'Insurance',
            'Maintenance',
            'Loans',
            'Locations',
            'Documents',
        ]);
});

it('shows the valuations of a copy on its timeline, oldest first', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $newer = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 25000,
        'valued_at' => '2026-03-01',
        'type' => ValuationType::ProfessionalAppraisal,
    ]);
    $older = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 10000,
        'valued_at' => '2024-01-01',
        'type' => ValuationType::UserEstimate,
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-valuation-'.$older->id.'"', false)
        ->assertSee('data-test="history-valuation-'.$newer->id.'"', false)
        ->assertSeeInOrder(['Valued at $100', 'Own estimate', 'Valued at $250', 'Professional appraisal'])
        ->assertSeeInOrder(['Jan 2024', 'Mar 2026']);
});

it('shows the empty state when nothing has been recorded against a copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="no-history"', false)
        ->assertSee('Nothing has been recorded against this copy yet.');
});

it('shows the empty state when the item has no copies at all', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="no-copies-to-track"', false)
        ->assertSee('This item has no copies, so there is nothing to track the history of.')
        ->assertDontSee('data-test="no-history"', false);
});

it('lets a viewer read the history of an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($viewer)->get(route('items.history.index', [$collection, $item]))->assertOk();
});

it('does not show the history of an item belonging to another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);

    $this->actingAs($user)->get(route('items.history.index', [$foreign, $item]))->assertNotFound();
});

it('does not show the history of an item that belongs to a different collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $other->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))->assertNotFound();
});
