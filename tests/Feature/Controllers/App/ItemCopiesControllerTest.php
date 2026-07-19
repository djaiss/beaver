<?php

declare(strict_types=1);
use App\Enums\CopyStatus;
use App\Enums\PermissionEnum;
use App\Enums\TransactionType;
use App\Models\Collection;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the copies of an item with their condition and location', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $condition = Condition::factory()->create(['account_id' => $user->account_id, 'name' => 'Near Mint']);
    $location = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Display Case']);
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'condition_id' => $condition->id,
        'current_location_id' => $location->id,
    ]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 42000, 'valued_at' => '2026-01-01']);

    $response = $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]));

    $response->assertOk();
    $response->assertSee('data-test="copy-'.$copy->id.'"', false);
    $response->assertSee('Near Mint');
    $response->assertSee('Display Case');
    $response->assertSee('$420');
});

// The copy carries no value of its own any more, so the figure shown has to be
// the most recent valuation rather than the first or the sum of them.
it('shows the latest valuation of a copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 10000, 'valued_at' => '2024-01-01']);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 25000, 'valued_at' => '2026-01-01']);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('latest valuation')
        ->assertSee('$250')
        ->assertDontSee('$100');
});

it('shows a dash when a copy has never been valued', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('latest valuation')
        ->assertDontSee('total est. value');
});

it('shows the status of a copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id, 'status' => CopyStatus::Loaned]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="copy-status"', false)
        ->assertSee('Loaned out');
});

it('shows the identifier of a copy when it has one', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id, 'identifier' => 'CENTRAL-PERK-01']);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="copy-identifier"', false)
        ->assertSee('CENTRAL-PERK-01');
});

it('does not show an identifier chip when the copy has none', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id, 'identifier' => null]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertDontSee('data-test="copy-identifier"', false);
});

// One instance is the ordinary case, so calling it out would only be noise.
it('calls out the quantity only when the copy stands for more than one instance', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $single = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id, 'quantity' => 6]);
    Copy::factory()->create(['item_id' => $single->id, 'quantity' => 1]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="copy-quantity"', false)
        ->assertSee('× 6');

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $single]))
        ->assertOk()
        ->assertDontSee('data-test="copy-quantity"', false);
});

it('shows the note of a copy, and says so when there is none', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $noted = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $noted->id, 'note' => 'Signed by Gunther.']);
    Copy::factory()->create(['item_id' => $item->id, 'note' => null]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $noted]))
        ->assertOk()
        ->assertSee('Signed by Gunther.');

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('No note on this copy.');
});

it('links a copy to the history tab', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="copy-history-link"', false)
        ->assertSee(route('items.history.index', [$collection, $item]), false);
});

it('sums what every copy was last valued at', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $first = Copy::factory()->create(['item_id' => $item->id]);
    $second = Copy::factory()->create(['item_id' => $item->id]);
    Valuation::factory()->create(['copy_id' => $first->id, 'amount' => 30000, 'valued_at' => '2026-01-01']);
    Valuation::factory()->create(['copy_id' => $second->id, 'amount' => 20000, 'valued_at' => '2026-01-01']);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('2 physical copies')
        ->assertSee('total est. value $500');
});

it('tells the reader when an item has no copies', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="no-copies"', false)
        ->assertSee('No copies of this item yet.');
});

it('marks the copies tab as the current page', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="item-tab-copies"', false)
        ->assertSee('aria-current="page"', false);
});

it('lets a viewer read the copies of an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($viewer)->get(route('items.copies.index', [$collection, $item]))->assertOk();
});

it('does not show the copies of an item belonging to another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$foreign, $item]))->assertNotFound();
});

it('does not show the copies of an item that belongs to a different collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $other->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))->assertNotFound();
});

// The acquisition date and the price paid are not columns on the copy. Both are
// read from the earliest transaction that brought it in.
it('shows the acquisition date and the price paid of a copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Purchase,
        'amount' => 10000,
        'tax_amount' => 500,
        'total_amount' => null,
        'currency_code' => 'USD',
        'occurred_at' => '2024-06-02',
    ]);
    Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Sale,
        'amount' => 90000,
        'currency_code' => 'USD',
        'occurred_at' => '2026-01-01',
    ]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="copy-acquired-at"', false)
        ->assertSee('data-test="copy-price-paid"', false)
        ->assertSee('Jun 2024')
        ->assertSee('$105');
});

it('leaves the acquisition facts empty when nothing says how the copy arrived', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Sale,
        'amount' => 90000,
        'currency_code' => 'USD',
        'occurred_at' => '2026-01-01',
    ]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('Acquired')
        ->assertSee('Price paid')
        ->assertDontSee('$900');
});
