<?php

declare(strict_types=1);

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Location;
use App\Models\Set;
use App\Models\Transaction;
use App\Models\Valuation;
use App\Services\CollectionStatistics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;

uses(RefreshDatabase::class);

/**
 * A copy worth a given amount, which now takes two rows rather than one column.
 */
function valuedCopy(array $attributes, int $amount): Copy
{
    $copy = Copy::factory()->create($attributes);

    Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => $amount,
        'valued_at' => '2026-01-01',
    ]);

    return $copy;
}

/**
 * A copy acquired on a given date, which is the date of the transaction that
 * brought it in rather than a column on the copy.
 */
function acquiredCopy(array $attributes, int $amount, string $acquiredAt): Copy
{
    $copy = valuedCopy($attributes, $amount);

    Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Purchase,
        'occurred_at' => $acquiredAt,
    ]);

    return $copy;
}

it('counts the items and sums what the copies are worth', function (): void {
    $collection = Collection::factory()->create();
    $spiderMan = Item::factory()->create(['collection_id' => $collection->id]);
    $xMen = Item::factory()->create(['collection_id' => $collection->id]);
    valuedCopy(['item_id' => $spiderMan->id], 60000);
    valuedCopy(['item_id' => $spiderMan->id], 20000);
    valuedCopy(['item_id' => $xMen->id], 40000);

    $totals = new CollectionStatistics(collection: $collection)->totals();

    expect($totals['items'])->toBe(2)
        ->and($totals['copies'])->toBe(3)
        ->and($totals['value'])->toBe(120000)
        ->and($totals['average'])->toBe(60000);
});

// The whole point of the valuations table: what a copy is worth is the most
// recent reading, not the first one and not the sum of them.
it('reads what a copy is worth from its latest valuation', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 10000, 'valued_at' => '2024-01-01']);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 25000, 'valued_at' => '2026-01-01']);

    expect(new CollectionStatistics(collection: $collection)->totals()['value'])->toBe(25000);
});

// Two valuations on the same day would otherwise be picked between arbitrarily.
it('breaks a tie on the valuation date with the newer row', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 10000, 'valued_at' => '2026-01-01']);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 20000, 'valued_at' => '2026-01-01']);

    expect(new CollectionStatistics(collection: $collection)->totals()['value'])->toBe(20000);
});

it('counts a copy nobody has valued as worth nothing', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);
    valuedCopy(['item_id' => $item->id], 30000);

    $totals = new CollectionStatistics(collection: $collection)->totals();

    expect($totals['copies'])->toBe(2)
        ->and($totals['value'])->toBe(30000);
});

it('ignores the items of another collection', function (): void {
    $collection = Collection::factory()->create();
    $other = Collection::factory()->create();
    valuedCopy(['item_id' => Item::factory()->create(['collection_id' => $collection->id])->id], 5000);
    valuedCopy(['item_id' => Item::factory()->create(['collection_id' => $other->id])->id], 999900);

    $totals = new CollectionStatistics(collection: $collection)->totals();

    expect($totals['items'])->toBe(1)
        ->and($totals['value'])->toBe(5000);
});

// A copy is undated when no transaction says how it was acquired, which is not
// the same as having been acquired on an unknown date.
it('counts the copies no transaction says how it was acquired', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    acquiredCopy(['item_id' => $item->id], 10000, '2026-03-01');
    Copy::factory()->count(2)->create(['item_id' => $item->id]);

    $totals = new CollectionStatistics(collection: $collection)->totals();

    expect($totals['copies'])->toBe(3)
        ->and($totals['undatedCopies'])->toBe(2);
});

// A fee is money around an acquisition, not the acquisition itself, so it must
// not give the copy a date it never got.
it('does not treat a fee as the acquisition of a copy', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = valuedCopy(['item_id' => $item->id], 10000);
    Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Fee,
        'occurred_at' => '2026-03-01',
    ]);

    expect(new CollectionStatistics(collection: $collection)->totals()['undatedCopies'])->toBe(1);
});

it('adds up what was acquired this month', function (): void {
    Date::setTestNow('2026-07-15');

    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    acquiredCopy(['item_id' => $item->id], 75000, '2026-07-02');
    acquiredCopy(['item_id' => $item->id], 20000, '2026-06-02');

    expect(new CollectionStatistics(collection: $collection)->totals()['valueAddedThisMonth'])->toBe(75000);

    Date::setTestNow();
});

it('has no average when the collection is empty', function (): void {
    $collection = Collection::factory()->create();

    $totals = new CollectionStatistics(collection: $collection)->totals();

    expect($totals['items'])->toBe(0)
        ->and($totals['average'])->toBe(0);
});

it('measures how far along the sets are', function (): void {
    $collection = Collection::factory()->create();
    $friends = Set::factory()->create(['collection_id' => $collection->id, 'target_count' => 10]);
    Item::factory()->count(4)->create(['collection_id' => $collection->id, 'set_id' => $friends->id]);

    $sets = new CollectionStatistics(collection: $collection)->setsCompletion();

    expect($sets['percentage'])->toBe(40)
        ->and($sets['owned'])->toBe(4)
        ->and($sets['target'])->toBe(10)
        ->and($sets['remaining'])->toBe(6);
});

it('does not let an overfilled set count as more than done', function (): void {
    $collection = Collection::factory()->create();
    $friends = Set::factory()->create(['collection_id' => $collection->id, 'target_count' => 3]);
    Item::factory()->count(7)->create(['collection_id' => $collection->id, 'set_id' => $friends->id]);

    $sets = new CollectionStatistics(collection: $collection)->setsCompletion();

    expect($sets['percentage'])->toBe(100)
        ->and($sets['remaining'])->toBe(0);
});

it('has no set completion when no set carries a target', function (): void {
    $collection = Collection::factory()->create();
    Set::factory()->create(['collection_id' => $collection->id, 'target_count' => null]);

    expect(new CollectionStatistics(collection: $collection)->setsCompletion())->toBeNull();
});

// The line is a running total, so a copy acquired before the window still has
// to be in the first point rather than appearing out of nowhere.
it('runs the value line as a total, carrying what came before the window', function (): void {
    Date::setTestNow('2026-07-15');

    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    acquiredCopy(['item_id' => $item->id], 10000, '2020-01-01');
    acquiredCopy(['item_id' => $item->id], 5000, '2026-07-02');

    $value = new CollectionStatistics(collection: $collection)->valueOverTime();

    expect($value)->toHaveCount(12)
        ->and($value[0]['value'])->toBe(10000)
        ->and($value[11]['label'])->toBe('Jul')
        ->and($value[11]['value'])->toBe(15000);

    Date::setTestNow();
});

it('counts the acquisitions of each month', function (): void {
    Date::setTestNow('2026-07-15');

    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    acquiredCopy(['item_id' => $item->id], 100, '2026-07-02');
    acquiredCopy(['item_id' => $item->id], 100, '2026-07-20');
    acquiredCopy(['item_id' => $item->id], 100, '2026-06-02');
    Copy::factory()->create(['item_id' => $item->id]);

    $acquisitions = new CollectionStatistics(collection: $collection)->acquisitionsPerMonth();

    expect($acquisitions)->toHaveCount(12)
        ->and($acquisitions[11]['label'])->toBe('Jul')
        ->and($acquisitions[11]['count'])->toBe(2)
        ->and($acquisitions[10]['count'])->toBe(1);

    Date::setTestNow();
});

it('spreads the items across the categories, biggest first', function (): void {
    $collection = Collection::factory()->create();
    $spiderMan = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    $xMen = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'X-Men']);
    Item::factory()->count(3)->create(['collection_id' => $collection->id, 'category_id' => $spiderMan->id]);
    Item::factory()->create(['collection_id' => $collection->id, 'category_id' => $xMen->id]);

    $categories = new CollectionStatistics(collection: $collection)->byCategory();

    expect($categories)->toHaveCount(2)
        ->and($categories[0])->toBe(['label' => 'Spider-Man', 'other' => false, 'count' => 3, 'percentage' => 75])
        ->and($categories[1])->toBe(['label' => 'X-Men', 'other' => false, 'count' => 1, 'percentage' => 25]);
});

it('keeps every category whole in the breakdown, with what it is worth', function (): void {
    $collection = Collection::factory()->create();
    $spiderMan = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    $xMen = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'X-Men']);
    $empty = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Wolverine']);

    $amazing = Item::factory()->create(['collection_id' => $collection->id, 'category_id' => $spiderMan->id]);
    Item::factory()->create(['collection_id' => $collection->id, 'category_id' => $spiderMan->id]);
    $newMutants = Item::factory()->create(['collection_id' => $collection->id, 'category_id' => $xMen->id]);
    valuedCopy(['item_id' => $amazing->id], 60000);
    valuedCopy(['item_id' => $newMutants->id], 40000);

    $breakdown = new CollectionStatistics(collection: $collection)->categoryBreakdown();

    expect($breakdown)->toHaveCount(3)
        ->and($breakdown[0])->toBe(['id' => $spiderMan->id, 'name' => 'Spider-Man', 'count' => 2, 'value' => 60000])
        ->and($breakdown[1])->toBe(['id' => $xMen->id, 'name' => 'X-Men', 'count' => 1, 'value' => 40000])
        ->and($breakdown[2])->toBe(['id' => $empty->id, 'name' => 'Wolverine', 'count' => 0, 'value' => 0]);
});

it('has an empty breakdown when the collection has no categories', function (): void {
    $collection = Collection::factory()->create();
    Item::factory()->create(['collection_id' => $collection->id, 'category_id' => null]);

    expect(new CollectionStatistics(collection: $collection)->categoryBreakdown())->toBe([]);
});

it('leaves the categories of another collection out of the breakdown', function (): void {
    $collection = Collection::factory()->create();
    $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    Item::factory()->create(['collection_id' => $collection->id, 'category_id' => $category->id]);

    $other = Collection::factory()->create();
    $otherCategory = Category::factory()->create(['collection_id' => $other->id, 'name' => 'X-Men']);
    $otherItem = Item::factory()->create(['collection_id' => $other->id, 'category_id' => $otherCategory->id]);
    valuedCopy(['item_id' => $otherItem->id], 99000);

    $breakdown = new CollectionStatistics(collection: $collection)->categoryBreakdown();

    expect($breakdown)->toHaveCount(1)
        ->and($breakdown[0]['name'])->toBe('Spider-Man');
});

it('gathers the items that sit in no category', function (): void {
    $collection = Collection::factory()->create();
    $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    Item::factory()->create(['collection_id' => $collection->id, 'category_id' => $category->id]);
    Item::factory()->create(['collection_id' => $collection->id, 'category_id' => null]);

    $categories = new CollectionStatistics(collection: $collection)->byCategory();

    expect($categories)->toHaveCount(2)
        ->and($categories[1]['label'])->toBeNull()
        ->and($categories[1]['other'])->toBeFalse()
        ->and($categories[1]['count'])->toBe(1);
});

it('sums the smallest categories into a single slice', function (): void {
    $collection = Collection::factory()->create();

    foreach (range(1, 8) as $rank) {
        $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Category '.$rank]);
        Item::factory()->count(10 - $rank)->create(['collection_id' => $collection->id, 'category_id' => $category->id]);
    }

    $categories = new CollectionStatistics(collection: $collection)->byCategory();

    // The six biggest, then the two smallest (3 items and 2 items) rolled up.
    expect($categories)->toHaveCount(7)
        ->and($categories[6]['other'])->toBeTrue()
        ->and($categories[6]['count'])->toBe(5);
});

it('groups the copies by the condition they are in', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $mint = ItemCondition::factory()->create(['account_id' => $collection->account_id, 'name' => 'Mint']);
    Copy::factory()->count(3)->create(['item_id' => $item->id, 'item_condition_id' => $mint->id]);
    Copy::factory()->create(['item_id' => $item->id, 'item_condition_id' => null]);

    $conditions = new CollectionStatistics(collection: $collection)->byCondition();

    expect($conditions)->toHaveCount(2)
        ->and($conditions[0])->toBe(['label' => 'Mint', 'count' => 3, 'percentage' => 75])
        ->and($conditions[1]['label'])->toBeNull();
});

it('sums the value sitting in each location', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $attic = Location::factory()->create(['account_id' => $collection->account_id, 'name' => 'Attic']);
    $garage = Location::factory()->create(['account_id' => $collection->account_id, 'name' => 'Garage']);
    valuedCopy(['item_id' => $item->id, 'current_location_id' => $attic->id], 30000);
    valuedCopy(['item_id' => $item->id, 'current_location_id' => $garage->id], 80000);

    $locations = new CollectionStatistics(collection: $collection)->valueByLocation();

    expect($locations)->toBe([
        ['label' => 'Garage', 'value' => 80000],
        ['label' => 'Attic', 'value' => 30000],
    ]);
});

it('ranks the items by what all of their copies are worth together', function (): void {
    $collection = Collection::factory()->create();
    $rachel = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Rachel Green']);
    $monica = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Monica Geller']);
    valuedCopy(['item_id' => $rachel->id], 30000);
    valuedCopy(['item_id' => $rachel->id], 30000);
    valuedCopy(['item_id' => $monica->id], 50000);

    $top = new CollectionStatistics(collection: $collection)->topItems();

    expect($top)->toHaveCount(2)
        ->and($top[0]['item']->name)->toBe('Rachel Green')
        ->and($top[0]['value'])->toBe(60000)
        ->and($top[1]['item']->name)->toBe('Monica Geller');
});

it('leaves out the items no copy has put a value on', function (): void {
    $collection = Collection::factory()->create();
    Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Rachel Green']);
    $monica = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Monica Geller']);
    valuedCopy(['item_id' => $monica->id], 50000);

    $top = new CollectionStatistics(collection: $collection)->topItems();

    expect($top)->toHaveCount(1)
        ->and($top[0]['item']->name)->toBe('Monica Geller');
});

it('names the condition and the location of the copy carrying the most value', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Rachel Green']);
    $mint = ItemCondition::factory()->create(['account_id' => $collection->account_id, 'name' => 'Mint']);
    $attic = Location::factory()->create(['account_id' => $collection->account_id, 'name' => 'Attic']);
    valuedCopy(['item_id' => $item->id, 'item_condition_id' => null, 'current_location_id' => null], 100);
    valuedCopy(['item_id' => $item->id, 'item_condition_id' => $mint->id, 'current_location_id' => $attic->id], 90000);

    $top = new CollectionStatistics(collection: $collection)->topItems();

    expect($top[0]['condition'])->toBe('Mint')
        ->and($top[0]['location'])->toBe('Attic');
});
