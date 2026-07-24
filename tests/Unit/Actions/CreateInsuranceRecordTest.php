<?php

declare(strict_types=1);
use App\Actions\CreateInsuranceRecord;
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
function copyToInsure(User $user, ?string $currency = 'USD'): Copy
{
    $catalog = Catalog::factory()->create([
        'account_id' => $user->account_id,
        'currency' => $currency,
    ]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('records a piece of coverage', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);

    $record = new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Collectibles Insurance Services',
        insuredValue: 45000,
        status: InsuranceStatus::Active,
        policyNumber: 'CIS-88231',
        coverageType: 'Scheduled item',
        deductibleAmount: 10000,
        startsAt: '2024-01-01',
        isScheduledItem: true,
        contactName: 'Dana Whitfield',
        note: 'On the fine-collectibles rider.',
    )->execute();

    expect($record)->toBeInstanceOf(InsuranceRecord::class);
    expect($record->provider)->toBe('Collectibles Insurance Services');
    expect($record->insured_value)->toBe(45000);
    expect($record->status)->toBe(InsuranceStatus::Active);
    expect($record->is_scheduled_item)->toBeTrue();

    $this->assertDatabaseHas('insurance_records', [
        'id' => $record->id,
        'copy_id' => $copy->id,
        'insured_value' => 45000,
    ]);
});

it('falls back to the currency of the collection for both amounts', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross, 'EUR');

    $record = new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Allianz',
        insuredValue: 100,
        deductibleAmount: 50,
    )->execute();

    expect($record->currency_code)->toBe('EUR');
    expect($record->deductible_currency_code)->toBe('EUR');
});

it('leaves the deductible currency null when there is no deductible', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);

    $record = new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();

    expect($record->deductible_amount)->toBeNull();
    expect($record->deductible_currency_code)->toBeNull();
});

it('defaults the status to active', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);

    $record = new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();

    expect($record->status)->toBe(InsuranceStatus::Active);
});

// Two live records for the same policy would each claim to be the coverage in
// force, so a second active record under the same policy number is refused.
it('refuses a second active record for the same policy', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);

    InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Active,
        'policy_number' => 'CIS-88231',
    ]);

    new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Collectibles Insurance Services',
        insuredValue: 50000,
        status: InsuranceStatus::Active,
        policyNumber: 'CIS-88231',
    )->execute();
})->throws(ValidationException::class);

it('allows a second active record under a different policy', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);

    InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Active,
        'policy_number' => 'CIS-88231',
    ]);

    $record = new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Allstate',
        insuredValue: 30000,
        status: InsuranceStatus::Active,
        policyNumber: 'AL-33120',
    )->execute();

    expect($record)->toBeInstanceOf(InsuranceRecord::class);
});

it('allows a second record for the same policy when it is not active', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);

    InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Active,
        'policy_number' => 'CIS-88231',
    ]);

    $record = new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Collectibles Insurance Services',
        insuredValue: 50000,
        status: InsuranceStatus::Expired,
        policyNumber: 'CIS-88231',
    )->execute();

    expect($record->status)->toBe(InsuranceStatus::Expired);
});

it('stamps who created it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);

    $record = new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();

    expect($record->created_by_id)->toBe($ross->id);
    expect($record->created_by_name)->toBe($ross->getFullName());
    expect($record->updated_by_id)->toBe($ross->id);
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);

    new CreateInsuranceRecord(
        user: $ross,
        copy: $copy,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::InsuranceRecordCreation);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::InsuranceRecordCreation);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);
    $gunther = $this->createUser();

    new CreateInsuranceRecord(
        user: $gunther,
        copy: $copy,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToInsure($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new CreateInsuranceRecord(
        user: $phoebe,
        copy: $copy,
        provider: 'Allianz',
        insuredValue: 100,
    )->execute();
})->throws(ModelNotFoundException::class);
