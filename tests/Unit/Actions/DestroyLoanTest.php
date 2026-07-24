<?php

declare(strict_types=1);
use App\Actions\DestroyLoan;
use App\Enums\CopyStatus;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Models\Catalog;
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
        $catalog = Catalog::factory()->create(array_merge(['account_id' => $accountId], $attributes['catalog'] ?? []));
        $item = Item::factory()->create(['catalog_id' => $catalog->id]);

        return Copy::factory()->create(array_merge(['item_id' => $item->id], $attributes['copy'] ?? []));
    }
}

it('deletes the loan and its provenance events', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id);
    $loanEvent = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);
    $returnEvent = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);
    $loan = Loan::factory()->create([
        'copy_id' => $copy->id,
        'loan_provenance_event_id' => $loanEvent->id,
        'return_provenance_event_id' => $returnEvent->id,
    ]);

    new DestroyLoan(user: $user, loan: $loan)->execute();

    $this->assertModelMissing($loan);
    $this->assertModelMissing($loanEvent);
    $this->assertModelMissing($returnEvent);
});

it('brings the copy back into custody when the outstanding loan is deleted', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForLoan($user->account_id, ['copy' => ['status' => CopyStatus::Loaned]]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);

    new DestroyLoan(user: $user, loan: $loan)->execute();

    expect($copy->refresh()->status)->toBe(CopyStatus::Owned);
});

it('forbids a user who cannot manage the account', function () {
    $user = $this->createUser();
    $copy = copyForLoan($this->createAccount()->id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);

    expect(fn () => new DestroyLoan(user: $user, loan: $loan)->execute())
        ->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($loan);
});
