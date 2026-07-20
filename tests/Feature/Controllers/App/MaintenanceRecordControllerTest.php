<?php

declare(strict_types=1);
use App\Enums\MaintenanceType;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('logs work against a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $after = Condition::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->post(route('maintenanceRecords.create', [$collection, $item, $copy]), [
        'type' => MaintenanceType::Conservation->value,
        'title' => 'Archival cleaning and re-housing',
        'performed_by' => 'Atelier Restauration',
        'performed_at' => '2024-01-01',
        'cost_amount' => '120.50',
        'currency' => 'EUR',
        'condition_after_id' => (string) $after->id,
        'next_due_at' => '2025-01-01',
    ]);

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'maintenance']));
    $response->assertSessionHas('status', 'Maintenance record added');

    $record = MaintenanceRecord::query()->first();
    expect($record->copy_id)->toBe($copy->id);
    expect($record->type)->toBe(MaintenanceType::Conservation);
    expect($record->cost_amount)->toBe(12050);
    expect($record->cost_currency_code)->toBe('EUR');
    expect($copy->refresh()->condition_id)->toBe($after->id);
});

it('generates a provenance event when the toggle is on', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('maintenanceRecords.create', [$collection, $item, $copy]), [
        'type' => MaintenanceType::Restoration->value,
        'title' => 'Museum-grade restoration',
        'include_in_provenance' => '1',
    ]);

    expect(ProvenanceEvent::query()->count())->toBe(1);
    expect(MaintenanceRecord::query()->first()->provenance_event_id)->not->toBeNull();
});

it('requires a type and a title', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('maintenanceRecords.create', [$collection, $item, $copy]), [])
        ->assertSessionHasErrors(['type', 'title']);
});

it('does not log work against a copy of another account', function () {
    $user = $this->createUser();
    $otherCollection = Collection::factory()->create(['account_id' => $this->createAccount()->id]);
    $otherItem = Item::factory()->create(['collection_id' => $otherCollection->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $otherItem->id]);

    $this->actingAs($user)->post(route('maintenanceRecords.create', [$otherCollection, $otherItem, $otherCopy]), [
        'type' => MaintenanceType::Cleaning->value,
        'title' => 'Cleaning',
    ])->assertNotFound();
});

it('forbids a viewer from logging work', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($viewer)->post(route('maintenanceRecords.create', [$collection, $item, $copy]), [
        'type' => MaintenanceType::Cleaning->value,
        'title' => 'Cleaning',
    ])->assertNotFound();
});

it('updates a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'title' => 'Old title']);

    $response = $this->actingAs($user)->put(route('maintenanceRecords.update', [$collection, $item, $copy, $record]), [
        'type' => MaintenanceType::Restoration->value,
        'title' => 'Full restoration',
        'cost_amount' => '199.99',
    ]);

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'maintenance']));
    $response->assertSessionHas('status', 'Maintenance record updated');

    $record->refresh();
    expect($record->title)->toBe('Full restoration');
    expect($record->cost_amount)->toBe(19999);
});

it('does not update a record that belongs to another copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $item->id]);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $otherCopy->id]);

    $this->actingAs($user)->put(route('maintenanceRecords.update', [$collection, $item, $copy, $record]), [
        'type' => MaintenanceType::Cleaning->value,
        'title' => 'Cleaning',
    ])->assertNotFound();
});

it('deletes a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    $response = $this->actingAs($user)->delete(route('maintenanceRecords.destroy', [$collection, $item, $copy, $record]));

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'maintenance']));
    $this->assertModelMissing($record);
});

it('forbids a viewer from deleting a record', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->delete(route('maintenanceRecords.destroy', [$collection, $item, $copy, $record]))->assertNotFound();
});

it('shows the maintenance records of a copy on the history tab', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'maintenance']))
        ->assertOk()
        ->assertSee('data-test="maintenance-'.$record->id.'"', false)
        ->assertSee('data-test="new-maintenance-'.$copy->id.'"', false);
});

it('renders the add form with its type select and provenance toggle', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'maintenance']))
        ->assertOk()
        ->assertSee('data-test="add-maintenance-'.$copy->id.'-type"', false)
        ->assertSee('data-test="add-maintenance-'.$copy->id.'-provenance-toggle"', false)
        ->assertSee('data-test="add-maintenance-'.$copy->id.'-submit"', false);
});

it('does not render the maintenance form for a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->get(route('items.history.show', [$collection, $item, $copy, 'maintenance']))
        ->assertOk()
        ->assertDontSee('data-test="new-maintenance-'.$copy->id.'"', false);
});
