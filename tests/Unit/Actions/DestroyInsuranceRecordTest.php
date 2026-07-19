<?php

declare(strict_types=1);
use App\Actions\DestroyInsuranceRecord;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * An insurance record on a copy in a collection of the given user's account.
 */
function insuranceToDestroy(User $user): InsuranceRecord
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    return InsuranceRecord::factory()->create(['copy_id' => $copy->id]);
}

it('deletes a record', function () {
    Queue::fake();
    $ross = $this->createUser();
    $record = insuranceToDestroy($ross);

    new DestroyInsuranceRecord(
        user: $ross,
        record: $record,
    )->execute();

    $this->assertModelMissing($record);
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $record = insuranceToDestroy($ross);

    new DestroyInsuranceRecord(
        user: $ross,
        record: $record,
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::InsuranceRecordDeletion);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::InsuranceRecordDeletion);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $record = insuranceToDestroy($ross);
    $gunther = $this->createUser();

    new DestroyInsuranceRecord(
        user: $gunther,
        record: $record,
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $record = insuranceToDestroy($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new DestroyInsuranceRecord(
        user: $phoebe,
        record: $record,
    )->execute();
})->throws(ModelNotFoundException::class);
