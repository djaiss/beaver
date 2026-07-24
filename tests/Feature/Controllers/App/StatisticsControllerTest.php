<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Location;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * A copy worth a given amount, which now takes two rows rather than one column.
 */
function valuedCopyOnStatistics(array $attributes, int $amount): Copy
{
    $copy = Copy::factory()->create($attributes);

    Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => $amount,
        'valued_at' => '2026-01-01',
    ]);

    return $copy;
}

it('shows the statistics of a collection', function (): void {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Rachel Green']);
    valuedCopyOnStatistics(['item_id' => $item->id], 84200);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/statistics');

    $response->assertOk()
        ->assertSee('Total items')
        ->assertSee('Estimated value')
        ->assertSee('$842')
        ->assertSee('data-test="statistics-kpis"', false);
});

it('shows a breadcrumb back to the collection', function (): void {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/statistics');

    $response->assertOk()
        ->assertSeeInOrder(['Collections', 'Marvel Comics 1990s', 'Statistics'])
        ->assertSee(route('collections.index'), false)
        ->assertSee(route('collections.show', $catalog->id), false);
});

it('shows the empty state when the collection holds no item', function (): void {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/statistics');

    $response->assertOk()
        ->assertSee('Nothing to measure yet')
        ->assertSee('data-test="no-statistics"', false);
});

it('breaks the items down by category, condition and location', function (): void {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider-Man']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'category_id' => $category->id]);
    $condition = ItemCondition::factory()->create(['account_id' => $user->account_id, 'name' => 'Mint']);
    $location = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Attic']);
    valuedCopyOnStatistics(['item_id' => $item->id, 'item_condition_id' => $condition->id, 'current_location_id' => $location->id], 1000);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/statistics');

    $response->assertOk()
        ->assertSee('Spider-Man')
        ->assertSee('Mint')
        ->assertSee('Attic')
        ->assertSee('data-test="items-by-category"', false)
        ->assertSee('data-test="copies-by-condition"', false)
        ->assertSee('data-test="value-by-location"', false);
});

it('links every top item to its own page', function (): void {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Rachel Green']);
    valuedCopyOnStatistics(['item_id' => $item->id], 90000);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/statistics');

    $response->assertOk()
        ->assertSee('Rachel Green')
        ->assertSee('data-test="top-item-'.$item->id.'"', false)
        ->assertSee(route('items.show', [$catalog->id, $item->id]), false);
});

// The acquisition date left the copy with #117 and comes back with the
// transactions of #118, so for now every copy reads as undated. Flip this back
// deliberately when transactions land.
it('says how many copies are missing an acquisition date', function (): void {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/statistics');

    $response->assertOk()
        ->assertSee('data-test="undated-copies"', false)
        ->assertSee('1 copy has no acquisition date');
});

it('links the statistics from the sidebar of the collection', function (): void {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/categories');

    $response->assertOk()
        ->assertSee(route('statistics.index', $catalog->id), false)
        ->assertSee('Statistics');
});

it('lets a viewer see the statistics', function (): void {
    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $this->actingAs($user)->get('/collections/'.$catalog->id.'/statistics')->assertOk();
});

it('cannot see the statistics of a collection of another account', function (): void {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create();

    $this->actingAs($user)->get('/collections/'.$catalog->id.'/statistics')->assertNotFound();
});
