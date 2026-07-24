<?php

declare(strict_types=1);
use App\Actions\UpdateInsuranceRecord;
use App\Enums\InsuranceStatus;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

/**
 * A copy sitting in a collection of the given user's account.
 */
function copyForInsuranceUpdate(User $user): Copy
{
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('updates a record', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForInsuranceUpdate($ross);
    $record = InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'provider' => 'Allstate',
        'insured_value' => 30000,
        'status' => InsuranceStatus::Active,
    ]);

    $updated = new UpdateInsuranceRecord(
        user: $ross,
        record: $record,
        provider: 'Collectibles Insurance Services',
        insuredValue: 45000,
        status: InsuranceStatus::Active,
        policyNumber: 'CIS-88231',
    )->execute();

    expect($updated->provider)->toBe('Collectibles Insurance Services');
    expect($updated->insured_value)->toBe(45000);

    $this->assertDatabaseHas('insurance_records', [
        'id' => $record->id,
        'insured_value' => 45000,
    ]);
});

// A record may stay active on its own policy when edited: the guard only fires
// against a different record holding the same policy.
it('lets a record keep its own active policy when edited', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForInsuranceUpdate($ross);
    $record = InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Active,
        'policy_number' => 'CIS-88231',
    ]);

    $updated = new UpdateInsuranceRecord(
        user: $ross,
        record: $record,
        provider: 'Collectibles Insurance Services',
        insuredValue: 50000,
        status: InsuranceStatus::Active,
        policyNumber: 'CIS-88231',
    )->execute();

    expect($updated->insured_value)->toBe(50000);
});

it('refuses to revive a record into a policy another active record holds', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForInsuranceUpdate($ross);
    InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Active,
        'policy_number' => 'CIS-88231',
    ]);
    $expired = InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Expired,
        'policy_number' => 'CIS-88231',
    ]);

    new UpdateInsuranceRecord(
        user: $ross,
        record: $expired,
        provider: 'Collectibles Insurance Services',
        insuredValue: 45000,
        status: InsuranceStatus::Active,
        policyNumber: 'CIS-88231',
    )->execute();
})->throws(ValidationException::class);

it('stamps who updated it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForInsuranceUpdate($ross);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    $updated = new UpdateInsuranceRecord(
        user: $ross,
        record: $record,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();

    expect($updated->updated_by_id)->toBe($ross->id);
    expect($updated->updated_by_name)->toBe($ross->getFullName());
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForInsuranceUpdate($ross);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    new UpdateInsuranceRecord(
        user: $ross,
        record: $record,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::InsuranceRecordUpdate);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::InsuranceRecordUpdate);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForInsuranceUpdate($ross);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);
    $gunther = $this->createUser();

    new UpdateInsuranceRecord(
        user: $gunther,
        record: $record,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();
})->throws(ModelNotFoundException::class);
