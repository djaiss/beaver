<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Collection;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\Set;
use App\Services\CollectionStatistics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;

uses(RefreshDatabase::class);

it('counts the items and sums what the copies are worth', function (): void {
    $collection = Collection::factory()->create();
    $spiderMan = Item::factory()->create(['collection_id' => $collection->id]);
    $xMen = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $spiderMan->id, 'estimated_value' => 60000]);
    Copy::factory()->create(['item_id' => $spiderMan->id, 'estimated_value' => 20000]);
    Copy::factory()->create(['item_id' => $xMen->id, 'estimated_value' => 40000]);

    $totals = new CollectionStatistics(collection: $collection)->totals();

    expect($totals['items'])->toBe(2)
        ->and($totals['copies'])->toBe(3)
        ->and($totals['value'])->toBe(120000)
        ->and($totals['average'])->toBe(60000);
});

it('ignores the items of another collection', function (): void {
    $collection = Collection::factory()->create();
    $other = Collection::factory()->create();
    Copy::factory()->create(['item_id' => Item::factory()->create(['collection_id' => $collection->id])->id, 'estimated_value' => 5000]);
    Copy::factory()->create(['item_id' => Item::factory()->create(['collection_id' => $other->id])->id, 'estimated_value' => 999900]);

    $totals = new CollectionStatistics(collection: $collection)->totals();

    expect($totals['items'])->toBe(1)
        ->and($totals['value'])->toBe(5000);
});

it('reports how many copies have no acquisition date', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id, 'acquired_at' => null]);
    Copy::factory()->create(['item_id' => $item->id, 'acquired_at' => Date::now()]);

    expect(new CollectionStatistics(collection: $collection)->totals()['undatedCopies'])->toBe(1);
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

it('runs the value up month by month, carrying what came before the window', function (): void {
    Date::setTestNow('2026-07-15');

    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id, 'estimated_value' => 10000, 'acquired_at' => '2020-01-01']);
    Copy::factory()->create(['item_id' => $item->id, 'estimated_value' => 5000, 'acquired_at' => '2026-07-02']);

    $value = new CollectionStatistics(collection: $collection)->valueOverTime();

    expect($value)->toHaveCount(12)
        ->and($value[0]['value'])->toBe(10000)
        ->and($value[11]['value'])->toBe(15000)
        ->and($value[11]['label'])->toBe('Jul');

    Date::setTestNow();
});

it('counts the copies acquired each month', function (): void {
    Date::setTestNow('2026-07-15');

    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->count(3)->create(['item_id' => $item->id, 'acquired_at' => '2026-07-02']);
    Copy::factory()->create(['item_id' => $item->id, 'acquired_at' => '2026-06-02']);
    Copy::factory()->create(['item_id' => $item->id, 'acquired_at' => null]);

    $acquisitions = new CollectionStatistics(collection: $collection)->acquisitionsPerMonth();

    expect($acquisitions)->toHaveCount(12)
        ->and($acquisitions[11]['count'])->toBe(3)
        ->and($acquisitions[10]['count'])->toBe(1)
        ->and($acquisitions[0]['count'])->toBe(0);

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
    $mint = Condition::factory()->create(['account_id' => $collection->account_id, 'name' => 'Mint']);
    Copy::factory()->count(3)->create(['item_id' => $item->id, 'condition_id' => $mint->id]);
    Copy::factory()->create(['item_id' => $item->id, 'condition_id' => null]);

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
    Copy::factory()->create(['item_id' => $item->id, 'location_id' => $attic->id, 'estimated_value' => 30000]);
    Copy::factory()->create(['item_id' => $item->id, 'location_id' => $garage->id, 'estimated_value' => 80000]);

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
    Copy::factory()->create(['item_id' => $rachel->id, 'estimated_value' => 30000]);
    Copy::factory()->create(['item_id' => $rachel->id, 'estimated_value' => 30000]);
    Copy::factory()->create(['item_id' => $monica->id, 'estimated_value' => 50000]);

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
    Copy::factory()->create(['item_id' => $monica->id, 'estimated_value' => 50000]);

    $top = new CollectionStatistics(collection: $collection)->topItems();

    expect($top)->toHaveCount(1)
        ->and($top[0]['item']->name)->toBe('Monica Geller');
});

it('names the condition and the location of the copy carrying the most value', function (): void {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Rachel Green']);
    $mint = Condition::factory()->create(['account_id' => $collection->account_id, 'name' => 'Mint']);
    $attic = Location::factory()->create(['account_id' => $collection->account_id, 'name' => 'Attic']);
    Copy::factory()->create(['item_id' => $item->id, 'estimated_value' => 100, 'condition_id' => null, 'location_id' => null]);
    Copy::factory()->create(['item_id' => $item->id, 'estimated_value' => 90000, 'condition_id' => $mint->id, 'location_id' => $attic->id]);

    $top = new CollectionStatistics(collection: $collection)->topItems();

    expect($top[0]['condition'])->toBe('Mint')
        ->and($top[0]['location'])->toBe('Attic');
});
