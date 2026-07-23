<?php

declare(strict_types=1);
use App\Actions\UpdateLoan;
use App\Enums\CopyStatus;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ProvenanceEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

if (! function_exists('copyForLoan')) {
    function copyForLoan(int $accountId, array $attributes = []): Copy
    {
        $collection = Collection::factory()->create(array_merge(['account_id' => $accountId], $attributes['collection'] ?? []));
        $item = Item::factory()->create(['collection_id' => $collection->id]);

        return Copy::factory()->create(array_merge(['item_id' => $item->id], $attributes['copy'] ?? []));
    }
}

it('updates a loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'party' => 'Old party']);

    new UpdateLoan(
        user: $user,
        loan: $loan,
        direction: LoanDirection::Outgoing,
        status: LoanStatus::Active,
        party: 'The Whitney Museum',
        loanedAt: '2024-01-01',
    )->execute();

    expect($loan->refresh()->party)->toBe('The Whitney Museum');
    expect($loan->updated_by_id)->toBe($user->id);
});

it('puts the copy back in hand when the loan is edited to returned', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Loaned]]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);

    new UpdateLoan(
        user: $user,
        loan: $loan,
        direction: LoanDirection::Outgoing,
        status: LoanStatus::Returned,
        party: $loan->party,
        loanedAt: $loan->loaned_at->toDateString(),
        returnedAt: '2024-06-01',
    )->execute();

    expect($copy->refresh()->status)->toBe(CopyStatus::Owned);
});

it('takes the copy out of custody when the loan is edited to active', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Owned]]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Planned]);

    new UpdateLoan(
        user: $user,
        loan: $loan,
        direction: LoanDirection::Outgoing,
        status: LoanStatus::Active,
        party: $loan->party,
        loanedAt: $loan->loaned_at->toDateString(),
    )->execute();

    expect($copy->refresh()->status)->toBe(CopyStatus::Loaned);
});

it('generates the provenance event when the flag is turned on', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'include_in_provenance' => false]);

    new UpdateLoan(
        user: $user,
        loan: $loan,
        direction: $loan->direction,
        status: $loan->status,
        party: $loan->party,
        loanedAt: $loan->loaned_at->toDateString(),
        includeInProvenance: true,
    )->execute();

    expect(ProvenanceEvent::query()->count())->toBe(1);
    expect($loan->refresh()->loan_provenance_event_id)->not->toBeNull();
});

it('removes the provenance events when the flag is turned off', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    $loanEvent = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);
    $returnEvent = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);
    $loan = Loan::factory()->create([
        'copy_id' => $copy->id,
        'include_in_provenance' => true,
        'loan_provenance_event_id' => $loanEvent->id,
        'return_provenance_event_id' => $returnEvent->id,
    ]);

    new UpdateLoan(
        user: $user,
        loan: $loan,
        direction: $loan->direction,
        status: $loan->status,
        party: $loan->party,
        loanedAt: $loan->loaned_at->toDateString(),
        includeInProvenance: false,
    )->execute();

    expect(ProvenanceEvent::query()->count())->toBe(0);
    expect($loan->refresh()->loan_provenance_event_id)->toBeNull();
    expect($loan->return_provenance_event_id)->toBeNull();
});

it('forbids a user who cannot manage the account', function () {
    $loan = Loan::factory()->create(['copy_id' => copyForLoan($this->createAccount()->id)->id]);
    $user = $this->createUser();

    expect(fn () => new UpdateLoan(
        user: $user,
        loan: $loan,
        direction: LoanDirection::Outgoing,
        status: LoanStatus::Active,
        party: 'A gallery',
        loanedAt: '2024-01-01',
    )->execute())->toThrow(ModelNotFoundException::class);
});

it('blocks reopening a loan when the copy already has another open outgoing loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);
    $returned = Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Returned]);

    expect(fn () => new UpdateLoan(
        user: $user,
        loan: $returned,
        direction: LoanDirection::Outgoing,
        status: LoanStatus::Active,
        party: 'A gallery',
        loanedAt: '2024-01-01',
    )->execute())->toThrow(ValidationException::class);
});

it('lets the same open outgoing loan be saved without clashing with itself', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);

    $updated = new UpdateLoan(
        user: $user,
        loan: $loan,
        direction: LoanDirection::Outgoing,
        status: LoanStatus::Active,
        party: 'A gallery',
        loanedAt: '2024-01-01',
    )->execute();

    expect($updated->party)->toBe('A gallery');
});
