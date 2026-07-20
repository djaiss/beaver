<?php

declare(strict_types=1);
use App\Actions\ReturnLoan;
use App\Enums\CopyStatus;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\ProvenanceEventType;
use App\Models\Collection;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
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

it('closes the loan and brings the copy back into custody', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Loaned]]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);

    new ReturnLoan(user: $user, loan: $loan, returnedAt: '2024-06-01')->execute();

    expect($loan->refresh()->status)->toBe(LoanStatus::Returned);
    expect($loan->returned_at->toDateString())->toBe('2024-06-01');
    expect($copy->refresh()->status)->toBe(CopyStatus::Owned);
});

it('takes the condition on return as the copy current condition', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Loaned]]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'status' => LoanStatus::Overdue]);
    $condition = Condition::factory()->create(['account_id' => $user->account_id]);

    new ReturnLoan(user: $user, loan: $loan, returnedAt: '2024-06-01', conditionInId: $condition->id)->execute();

    expect($loan->refresh()->condition_in_id)->toBe($condition->id);
    expect($copy->refresh()->condition_id)->toBe($condition->id);
});

it('records the return in provenance when the loan is part of it', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    $loanEvent = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);
    $loan = Loan::factory()->create([
        'copy_id' => $copy->id,
        'status' => LoanStatus::Active,
        'include_in_provenance' => true,
        'loan_provenance_event_id' => $loanEvent->id,
    ]);

    new ReturnLoan(user: $user, loan: $loan, returnedAt: '2024-06-01')->execute();

    expect($loan->refresh()->return_provenance_event_id)->not->toBeNull();
    expect(ProvenanceEvent::query()->where('type', ProvenanceEventType::Return->value)->count())->toBe(1);
});

it('does not return an already closed loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'status' => LoanStatus::Returned]);

    expect(fn () => new ReturnLoan(user: $user, loan: $loan, returnedAt: '2024-06-01')->execute())
        ->toThrow(ModelNotFoundException::class);
});

it('forbids a user who cannot manage the account', function () {
    $user = $this->createUser();
    $copy = copyForLoan($this->createAccount()->id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'status' => LoanStatus::Active]);

    expect(fn () => new ReturnLoan(user: $user, loan: $loan, returnedAt: '2024-06-01')->execute())
        ->toThrow(ModelNotFoundException::class);
});
