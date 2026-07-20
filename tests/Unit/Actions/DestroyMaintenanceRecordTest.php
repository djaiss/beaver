<?php

declare(strict_types=1);
use App\Actions\CreateMaintenanceRecord;
use App\Actions\DestroyMaintenanceRecord;
use App\Enums\ItemActionEnum;
use App\Enums\MaintenanceType;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function copyToDestroyMaintenance(User $user): Copy
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('deletes the record', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToDestroyMaintenance($ross);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    new DestroyMaintenanceRecord(user: $ross, record: $record)->execute();

    $this->assertModelMissing($record);
});

it('removes the provenance event it generated', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToDestroyMaintenance($ross);

    $record = new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Restoration,
        title: 'Museum-grade restoration',
        includeInProvenance: true,
    )->execute();

    $eventId = $record->provenance_event_id;
    expect($eventId)->not->toBeNull();

    new DestroyMaintenanceRecord(user: $ross, record: $record)->execute();

    $this->assertModelMissing($record);
    expect(ProvenanceEvent::query()->find($eventId))->toBeNull();
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToDestroyMaintenance($ross);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    new DestroyMaintenanceRecord(user: $ross, record: $record)->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::MaintenanceRecordDeletion);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::MaintenanceRecordDeletion);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToDestroyMaintenance($ross);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new DestroyMaintenanceRecord(user: $phoebe, record: $record)->execute();
})->throws(ModelNotFoundException::class);
