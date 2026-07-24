<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\LocationHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('moves a copy to a location', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $shelf = Location::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->post(route('locationHistory.create', [$catalog, $item, $copy]), [
        'location_id' => $shelf->id,
        'moved_at' => '2024-01-01',
        'reason' => 'Catalogued',
    ]);

    $response->assertRedirect(route('items.history.show', [$catalog, $item, $copy, 'locations']));
    $response->assertSessionHas('status', 'Copy moved');

    expect($copy->refresh()->current_location_id)->toBe($shelf->id);
    expect(LocationHistory::query()->where('copy_id', $copy->id)->count())->toBe(1);
});

it('requires a destination and a date', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('locationHistory.create', [$catalog, $item, $copy]), [])
        ->assertSessionHasErrors(['location_id', 'moved_at']);
});

it('does not move a copy of another account', function () {
    $user = $this->createUser();
    $otherCatalog = Catalog::factory()->create(['account_id' => $this->createAccount()->id]);
    $otherItem = Item::factory()->create(['catalog_id' => $otherCatalog->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $otherItem->id]);
    $location = Location::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post(route('locationHistory.create', [$otherCatalog, $otherItem, $otherCopy]), [
        'location_id' => $location->id,
        'moved_at' => '2024-01-01',
    ])->assertNotFound();
});

it('forbids a viewer from moving a copy', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->post(route('locationHistory.create', [$catalog, $item, $copy]), [
        'location_id' => $location->id,
        'moved_at' => '2024-01-01',
    ])->assertNotFound();
});

it('corrects a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $shelf = Location::factory()->create(['account_id' => $user->account_id]);
    $record = LocationHistory::factory()->create(['copy_id' => $copy->id, 'location_id' => $shelf->id, 'moved_at' => '2024-01-01']);

    $response = $this->actingAs($user)->put(route('locationHistory.update', [$catalog, $item, $copy, $record]), [
        'location_id' => $shelf->id,
        'moved_at' => '2024-02-15',
        'reason' => 'Fixed the date',
    ]);

    $response->assertRedirect(route('items.history.show', [$catalog, $item, $copy, 'locations']));
    $record->refresh();
    expect($record->moved_at->toDateString())->toBe('2024-02-15');
    expect($record->reason)->toBe('Fixed the date');
});

it('deletes a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = LocationHistory::factory()->closed()->create(['copy_id' => $copy->id]);

    $response = $this->actingAs($user)->delete(route('locationHistory.destroy', [$catalog, $item, $copy, $record]));

    $response->assertRedirect(route('items.history.show', [$catalog, $item, $copy, 'locations']));
    $this->assertModelMissing($record);
});

it('forbids a viewer from deleting a record', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = LocationHistory::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->delete(route('locationHistory.destroy', [$catalog, $item, $copy, $record]))->assertNotFound();
});

it('shows the location history of a copy on the history tab', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $location = Location::factory()->create(['account_id' => $user->account_id]);
    $record = LocationHistory::factory()->create(['copy_id' => $copy->id, 'location_id' => $location->id]);

    $this->actingAs($user)->get(route('items.history.show', [$catalog, $item, $copy, 'locations']))
        ->assertOk()
        ->assertSee('data-test="location-'.$record->id.'"', false)
        ->assertSee('data-test="new-location-'.$copy->id.'"', false);
});

it('does not render the move form for a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    LocationHistory::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->get(route('items.history.show', [$catalog, $item, $copy, 'locations']))
        ->assertOk()
        ->assertDontSee('data-test="new-location-'.$copy->id.'"', false);
});
