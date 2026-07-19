<?php

declare(strict_types=1);
use App\Actions\UpdateValuation;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\User;
use App\Models\Valuation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * A valuation on a copy in a collection of the given user's account.
 */
function valuationToUpdate(User $user, array $attributes = []): Valuation
{
    $collection = Collection::factory()->create([
        'account_id' => $user->account_id,
        'currency' => 'USD',
    ]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    return Valuation::factory()->create([...$attributes, 'copy_id' => $copy->id]);
}

it('updates a valuation', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToUpdate($ross, [
        'type' => ValuationType::UserEstimate,
        'amount' => 10000,
        'confidence' => ValuationConfidence::Low,
    ]);

    $updated = new UpdateValuation(
        user: $ross,
        valuation: $valuation,
        type: ValuationType::ProfessionalAppraisal,
        amount: 25000,
        valuedAt: '2026-06-15',
        confidence: ValuationConfidence::High,
        valuer: 'Phoebe Buffay',
    )->execute();

    expect($updated->type)->toBe(ValuationType::ProfessionalAppraisal);
    expect($updated->amount)->toBe(25000);
    expect($updated->confidence)->toBe(ValuationConfidence::High);
    expect($updated->valuer)->toBe('Phoebe Buffay');
    expect($updated->valued_at->toDateString())->toBe('2026-06-15');

    $this->assertDatabaseHas('valuations', [
        'id' => $valuation->id,
        'amount' => 25000,
    ]);
});

it('keeps the currency it already had when none is given', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToUpdate($ross, ['currency_code' => 'GBP']);

    $updated = new UpdateValuation(
        user: $ross,
        valuation: $valuation,
        type: ValuationType::UserEstimate,
        amount: 5000,
        valuedAt: '2026-06-15',
    )->execute();

    expect($updated->currency_code)->toBe('GBP');
});

it('stamps who updated it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToUpdate($ross);

    $updated = new UpdateValuation(
        user: $ross,
        valuation: $valuation,
        type: ValuationType::UserEstimate,
        amount: 5000,
        valuedAt: '2026-06-15',
    )->execute();

    expect($updated->updated_by_id)->toBe($ross->id);
    expect($updated->updated_by_name)->toBe($ross->getFullName());
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToUpdate($ross);

    new UpdateValuation(
        user: $ross,
        valuation: $valuation,
        type: ValuationType::UserEstimate,
        amount: 5000,
        valuedAt: '2026-06-15',
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::ValuationUpdate);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::ValuationUpdate);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToUpdate($ross);
    $gunther = $this->createUser();

    new UpdateValuation(
        user: $gunther,
        valuation: $valuation,
        type: ValuationType::UserEstimate,
        amount: 5000,
        valuedAt: '2026-06-15',
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToUpdate($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new UpdateValuation(
        user: $phoebe,
        valuation: $valuation,
        type: ValuationType::UserEstimate,
        amount: 5000,
        valuedAt: '2026-06-15',
    )->execute();
})->throws(ModelNotFoundException::class);
