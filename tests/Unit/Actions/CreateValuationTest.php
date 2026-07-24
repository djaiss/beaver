<?php

declare(strict_types=1);
use App\Actions\CreateValuation;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\User;
use App\Models\Valuation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * A copy sitting in a collection of the given user's account.
 */
function copyToValue(User $user, ?string $currency = 'USD'): Copy
{
    $catalog = Catalog::factory()->create([
        'account_id' => $user->account_id,
        'currency' => $currency,
    ]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('records a valuation', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross);

    $valuation = new CreateValuation(
        user: $ross,
        copy: $copy,
        type: ValuationType::ProfessionalAppraisal,
        amount: 25000,
        valuedAt: '2026-01-08',
        confidence: ValuationConfidence::High,
        valuer: 'Central Perk Appraisals',
        method: 'Comparable sales',
        referenceNumber: 'CP-1994',
        note: 'Valued after the January sale.',
    )->execute();

    expect($valuation)->toBeInstanceOf(Valuation::class);
    expect($valuation->type)->toBe(ValuationType::ProfessionalAppraisal);
    expect($valuation->amount)->toBe(25000);
    expect($valuation->confidence)->toBe(ValuationConfidence::High);
    expect($valuation->valued_at->toDateString())->toBe('2026-01-08');
    expect($valuation->valuer)->toBe('Central Perk Appraisals');

    $this->assertDatabaseHas('valuations', [
        'id' => $valuation->id,
        'copy_id' => $copy->id,
        'amount' => 25000,
    ]);
});

// The amount is what the copy reads its current worth from, so it flows through
// to the copy's estimated value once recorded.
it('becomes the current estimated value of the copy', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross);

    new CreateValuation(
        user: $ross,
        copy: $copy,
        type: ValuationType::UserEstimate,
        amount: 12000,
        valuedAt: '2026-02-01',
    )->execute();

    expect($copy->refresh()->estimatedValue())->toBe(12000);
});

// Every amount is in one currency, so the collection's is the sensible default
// rather than leaving it unset.
it('falls back to the currency of the collection', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross, 'EUR');

    $valuation = new CreateValuation(
        user: $ross,
        copy: $copy,
        type: ValuationType::UserEstimate,
        amount: 100,
        valuedAt: '2026-01-08',
    )->execute();

    expect($valuation->currency_code)->toBe('EUR');
});

it('keeps the currency it was given', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross, 'EUR');

    $valuation = new CreateValuation(
        user: $ross,
        copy: $copy,
        type: ValuationType::UserEstimate,
        amount: 100,
        valuedAt: '2026-01-08',
        currencyCode: 'GBP',
    )->execute();

    expect($valuation->currency_code)->toBe('GBP');
});

it('defaults the confidence to unknown', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross);

    $valuation = new CreateValuation(
        user: $ross,
        copy: $copy,
        type: ValuationType::UserEstimate,
        amount: 100,
        valuedAt: '2026-01-08',
    )->execute();

    expect($valuation->confidence)->toBe(ValuationConfidence::Unknown);
});

it('stamps who created it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross);

    $valuation = new CreateValuation(
        user: $ross,
        copy: $copy,
        type: ValuationType::UserEstimate,
        amount: 100,
        valuedAt: '2026-01-08',
    )->execute();

    expect($valuation->created_by_id)->toBe($ross->id);
    expect($valuation->created_by_name)->toBe($ross->getFullName());
    expect($valuation->updated_by_id)->toBe($ross->id);
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross);

    new CreateValuation(
        user: $ross,
        copy: $copy,
        type: ValuationType::UserEstimate,
        amount: 100,
        valuedAt: '2026-01-08',
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::ValuationCreation);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::ValuationCreation);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross);
    $gunther = $this->createUser();

    new CreateValuation(
        user: $gunther,
        copy: $copy,
        type: ValuationType::UserEstimate,
        amount: 100,
        valuedAt: '2026-01-08',
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToValue($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new CreateValuation(
        user: $phoebe,
        copy: $copy,
        type: ValuationType::UserEstimate,
        amount: 100,
        valuedAt: '2026-01-08',
    )->execute();
})->throws(ModelNotFoundException::class);
