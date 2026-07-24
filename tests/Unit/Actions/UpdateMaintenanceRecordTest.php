<?php

declare(strict_types=1);
use App\Actions\CreateMaintenanceRecord;
use App\Actions\UpdateMaintenanceRecord;
use App\Enums\ItemActionEnum;
use App\Enums\MaintenanceType;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function copyToUpdateMaintenance(User $user, ?string $currency = 'USD'): Copy
{
    $catalog = Catalog::factory()->create([
        'account_id' => $user->account_id,
        'currency' => $currency,
    ]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('updates the work', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToUpdateMaintenance($ross);
    $record = MaintenanceRecord::factory()->create([
        'copy_id' => $copy->id,
        'type' => MaintenanceType::Cleaning,
        'title' => 'Light cleaning',
    ]);

    new UpdateMaintenanceRecord(
        user: $ross,
        record: $record,
        type: MaintenanceType::Restoration,
        title: 'Full restoration',
        costAmount: 25000,
    )->execute();

    $record->refresh();
    expect($record->type)->toBe(MaintenanceType::Restoration);
    expect($record->title)->toBe('Full restoration');
    expect($record->cost_amount)->toBe(25000);
});

it('updates the copy condition to the condition after the work', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToUpdateMaintenance($ross);
    $after = ItemCondition::factory()->create(['account_id' => $ross->account_id]);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    new UpdateMaintenanceRecord(
        user: $ross,
        record: $record,
        type: MaintenanceType::Restoration,
        title: 'Full restoration',
        itemConditionAfterId: $after->id,
    )->execute();

    expect($copy->refresh()->item_condition_id)->toBe($after->id);
});

it('generates a provenance event when the flag is newly turned on', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToUpdateMaintenance($ross);
    $record = MaintenanceRecord::factory()->create([
        'copy_id' => $copy->id,
        'include_in_provenance' => false,
        'provenance_event_id' => null,
    ]);

    new UpdateMaintenanceRecord(
        user: $ross,
        record: $record,
        type: MaintenanceType::Restoration,
        title: 'Now significant',
        includeInProvenance: true,
    )->execute();

    $record->refresh();
    expect($record->provenance_event_id)->not->toBeNull();
    expect(ProvenanceEvent::query()->count())->toBe(1);
});

it('removes the provenance event when the flag is turned off', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToUpdateMaintenance($ross);

    $record = new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Restoration,
        title: 'Museum-grade restoration',
        includeInProvenance: true,
    )->execute();

    $eventId = $record->provenance_event_id;
    expect($eventId)->not->toBeNull();

    new UpdateMaintenanceRecord(
        user: $ross,
        record: $record,
        type: MaintenanceType::Restoration,
        title: 'Museum-grade restoration',
        includeInProvenance: false,
    )->execute();

    $record->refresh();
    expect($record->provenance_event_id)->toBeNull();
    expect(ProvenanceEvent::query()->find($eventId))->toBeNull();
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToUpdateMaintenance($ross);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    new UpdateMaintenanceRecord(
        user: $ross,
        record: $record,
        type: MaintenanceType::Cleaning,
        title: 'Cleaning',
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::MaintenanceRecordUpdate);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::MaintenanceRecordUpdate);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToUpdateMaintenance($ross);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new UpdateMaintenanceRecord(
        user: $phoebe,
        record: $record,
        type: MaintenanceType::Cleaning,
        title: 'Cleaning',
    )->execute();
})->throws(ModelNotFoundException::class);
