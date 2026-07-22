<?php

declare(strict_types=1);
use App\Actions\CreateMaintenanceRecord;
use App\Enums\ItemActionEnum;
use App\Enums\MaintenanceType;
use App\Enums\ProvenanceEventType;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Collection;
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

/**
 * A copy sitting in a collection of the given user's account.
 */
function copyToMaintain(User $user, ?string $currency = 'USD'): Copy
{
    $collection = Collection::factory()->create([
        'account_id' => $user->account_id,
        'currency' => $currency,
    ]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('logs a piece of work', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);

    $record = new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Conservation,
        title: 'Archival cleaning and re-housing',
        performedBy: 'Atelier Restauration',
        performedAt: '2024-01-01',
        costAmount: 12000,
    )->execute();

    expect($record)->toBeInstanceOf(MaintenanceRecord::class);
    expect($record->type)->toBe(MaintenanceType::Conservation);
    expect($record->title)->toBe('Archival cleaning and re-housing');
    expect($record->cost_amount)->toBe(12000);

    $this->assertDatabaseHas('maintenance_records', [
        'id' => $record->id,
        'copy_id' => $copy->id,
        'cost_amount' => 12000,
    ]);
});

it('falls back to the currency of the collection for the cost', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross, 'EUR');

    $record = new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Repair,
        title: 'Spine repair',
        costAmount: 5000,
    )->execute();

    expect($record->cost_currency_code)->toBe('EUR');
});

it('leaves the cost currency null when there is no cost', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);

    $record = new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Inspection,
        title: 'Annual condition check',
    )->execute();

    expect($record->cost_amount)->toBeNull();
    expect($record->cost_currency_code)->toBeNull();
});

it('updates the copy condition to the condition after the work', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);
    $after = ItemCondition::factory()->create(['account_id' => $ross->account_id]);

    new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Restoration,
        title: 'Full restoration',
        itemConditionAfterId: $after->id,
    )->execute();

    expect($copy->refresh()->item_condition_id)->toBe($after->id);
});

it('leaves the copy condition untouched when no condition after is given', function () {
    Queue::fake();
    $ross = $this->createUser();
    $before = ItemCondition::factory()->create(['account_id' => $ross->account_id]);
    $copy = copyToMaintain($ross);
    $copy->update(['item_condition_id' => $before->id]);

    new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Cleaning,
        title: 'Light dusting',
    )->execute();

    expect($copy->refresh()->item_condition_id)->toBe($before->id);
});

it('generates a matching provenance event when marked for provenance', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);

    $record = new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Restoration,
        title: 'Museum-grade restoration',
        performedAt: '2024-01-01',
        includeInProvenance: true,
    )->execute();

    expect($record->provenance_event_id)->not->toBeNull();

    $event = ProvenanceEvent::query()->find($record->provenance_event_id);
    expect($event->copy_id)->toBe($copy->id);
    expect($event->type)->toBe(ProvenanceEventType::SignificantRestoration);
    expect($event->title)->toBe('Museum-grade restoration');
});

it('does not generate a provenance event when not marked for provenance', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);

    $record = new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Cleaning,
        title: 'Routine cleaning',
    )->execute();

    expect($record->provenance_event_id)->toBeNull();
    expect(ProvenanceEvent::query()->count())->toBe(0);
});

it('refuses a condition from another account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);
    $foreign = ItemCondition::factory()->create(['account_id' => $this->createAccount()->id]);

    new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Cleaning,
        title: 'Cleaning',
        itemConditionAfterId: $foreign->id,
    )->execute();
})->throws(ModelNotFoundException::class);

it('stamps who created it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);

    $record = new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Cleaning,
        title: 'Cleaning',
    )->execute();

    expect($record->created_by_id)->toBe($ross->id);
    expect($record->created_by_name)->toBe($ross->getFullName());
    expect($record->updated_by_id)->toBe($ross->id);
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);

    new CreateMaintenanceRecord(
        user: $ross,
        copy: $copy,
        type: MaintenanceType::Cleaning,
        title: 'Cleaning',
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::MaintenanceRecordCreation);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::MaintenanceRecordCreation);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);
    $gunther = $this->createUser();

    new CreateMaintenanceRecord(
        user: $gunther,
        copy: $copy,
        type: MaintenanceType::Cleaning,
        title: 'Cleaning',
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMaintain($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new CreateMaintenanceRecord(
        user: $phoebe,
        copy: $copy,
        type: MaintenanceType::Cleaning,
        title: 'Cleaning',
    )->execute();
})->throws(ModelNotFoundException::class);
