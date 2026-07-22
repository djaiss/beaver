<?php

declare(strict_types=1);
use App\Actions\CreateLoan;
use App\Enums\CopyStatus;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\ProvenanceEventType;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Loan;
use App\Models\ProvenanceEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

if (! function_exists('copyForLoan')) {
    function copyForLoan(int $accountId, array $attributes = []): Copy
    {
        $collection = Collection::factory()->create(array_merge(['account_id' => $accountId], $attributes['collection'] ?? []));
        $item = Item::factory()->create(['collection_id' => $collection->id]);

        return Copy::factory()->create(array_merge(['item_id' => $item->id], $attributes['copy'] ?? []));
    }
}

it('records a loan and returns it', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Owned]]);

    $loan = new CreateLoan(
        user: $user,
        copy: $copy,
        direction: LoanDirection::Outgoing,
        party: 'The Whitney Museum',
        loanedAt: '2024-01-01',
    )->execute();

    expect($loan)->toBeInstanceOf(Loan::class);
    $this->assertDatabaseHas('loans', ['id' => $loan->id, 'copy_id' => $copy->id]);
    expect($loan->created_by_id)->toBe($user->id);
});

it('takes the copy out of custody for an active outgoing loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Owned]]);

    new CreateLoan(
        user: $user,
        copy: $copy,
        direction: LoanDirection::Outgoing,
        party: 'A friend',
        loanedAt: '2024-01-01',
        status: LoanStatus::Active,
    )->execute();

    expect($copy->refresh()->status)->toBe(CopyStatus::Loaned);
});

it('leaves the copy status alone for a planned or incoming loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $planned = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Owned]]);
    $borrowed = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Owned]]);

    new CreateLoan(user: $user, copy: $planned, direction: LoanDirection::Outgoing, party: 'A gallery', loanedAt: '2024-01-01', status: LoanStatus::Planned)->execute();
    new CreateLoan(user: $user, copy: $borrowed, direction: LoanDirection::Incoming, party: 'A collector', loanedAt: '2024-01-01', status: LoanStatus::Active)->execute();

    expect($planned->refresh()->status)->toBe(CopyStatus::Owned);
    expect($borrowed->refresh()->status)->toBe(CopyStatus::Owned);
});

it('defaults the deposit currency to the collection currency', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id, ['collection' => ['currency' => 'EUR']]);

    $loan = new CreateLoan(
        user: $user,
        copy: $copy,
        direction: LoanDirection::Outgoing,
        party: 'A museum',
        loanedAt: '2024-01-01',
        depositAmount: 250000,
    )->execute();

    expect($loan->deposit_currency_code)->toBe('EUR');
});

it('generates a provenance event when marked for provenance', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);

    $loan = new CreateLoan(
        user: $user,
        copy: $copy,
        direction: LoanDirection::Outgoing,
        party: 'The Whitney Museum',
        loanedAt: '2024-01-01',
        includeInProvenance: true,
    )->execute();

    expect(ProvenanceEvent::query()->count())->toBe(1);
    expect(ProvenanceEvent::query()->first()->type)->toBe(ProvenanceEventType::Loan);
    expect($loan->refresh()->loan_provenance_event_id)->not->toBeNull();
});

it('guards a condition from another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    $foreign = ItemCondition::factory()->create(['account_id' => $this->createAccount()->id]);

    expect(fn () => new CreateLoan(
        user: $user,
        copy: $copy,
        direction: LoanDirection::Outgoing,
        party: 'A gallery',
        loanedAt: '2024-01-01',
        itemConditionOutId: $foreign->id,
    )->execute())->toThrow(ModelNotFoundException::class);
});

it('forbids a user who cannot manage the account', function () {
    $user = $this->createUser();
    $copy = copyForLoan($this->createAccount()->id);

    expect(fn () => new CreateLoan(
        user: $user,
        copy: $copy,
        direction: LoanDirection::Outgoing,
        party: 'A gallery',
        loanedAt: '2024-01-01',
    )->execute())->toThrow(ModelNotFoundException::class);
});

it('logs the creation', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);

    new CreateLoan(user: $user, copy: $copy, direction: LoanDirection::Outgoing, party: 'A gallery', loanedAt: '2024-01-01')->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::LoanCreation);
});
