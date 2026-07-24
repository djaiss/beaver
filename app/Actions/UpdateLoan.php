<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ItemActionEnum;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\UserActionEnum;
use App\Helpers\Money;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Loan;
use App\Models\User;
use App\Traits\GuardsLoanConditions;
use App\Traits\GuardsOverlappingLoans;
use App\Traits\SyncsLoanState;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update a loan recorded against a copy. Only owners and editors of its account
 * may do so.
 *
 * The copy's status is kept in step with the change, so a loan edited back to
 * returned puts the copy back in hand. Turning the provenance flag on generates
 * the matching events, and turning it off removes them.
 */
class UpdateLoan
{
    use GuardsLoanConditions;
    use GuardsOverlappingLoans;
    use SyncsLoanState;

    /**
     * The values that moved, captured before the loan is written so the activity
     * tab can show what they moved from.
     *
     * @var list<array{label: string, from: string|null, to: string|null}>
     */
    private array $changes = [];

    public function __construct(
        private readonly User $user,
        private readonly Loan $loan,
        private readonly LoanDirection $direction,
        private readonly LoanStatus $status,
        private readonly string $party,
        private readonly string $loanedAt,
        private readonly ?string $purpose = null,
        private readonly ?string $dueAt = null,
        private readonly ?string $returnedAt = null,
        private readonly ?int $itemConditionOutId = null,
        private readonly ?int $itemConditionInId = null,
        private readonly ?int $depositAmount = null,
        private readonly ?string $depositCurrencyCode = null,
        private readonly bool $includeInProvenance = false,
    ) {}

    public function execute(): Loan
    {
        $this->validate();
        $this->captureChanges();
        $this->update();
        $this->syncCopyStatus($this->loan->copy);
        $this->reconcileProvenance();
        $this->log();

        return $this->loan;
    }

    private function validate(): void
    {
        $account = $this->loan->copy->item->catalog->account;

        if (! $account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        $this->guardConditionsBelongToAccount($account, $this->itemConditionOutId, $this->itemConditionInId);
        $this->guardAgainstOverlappingLoan($this->loan->copy, $this->direction, $this->status, $this->loan->id);
    }

    /**
     * Read what is about to move, while the loan still holds its old values.
     */
    private function captureChanges(): void
    {
        $currency = $this->loan->deposit_currency_code;

        $this->changes = array_values(array_filter([
            $this->change('Direction', $this->loan->direction->label(), $this->direction->label()),
            $this->change('Status', $this->loan->status->label(), $this->status->label()),
            $this->change('Party', $this->loan->party, $this->party),
            $this->change('Loaned', $this->loan->loaned_at->toDateString(), $this->loanedAt),
            $this->change('Due', $this->loan->due_at?->toDateString(), $this->dueAt),
            $this->change('Returned', $this->loan->returned_at?->toDateString(), $this->returnedAt),
            $this->change(
                'Deposit',
                $this->loan->deposit_amount === null ? null : Money::format($this->loan->deposit_amount, $currency),
                $this->depositAmount === null ? null : Money::format($this->depositAmount, $this->depositCurrencyCode ?? $currency),
            ),
        ]));
    }

    /**
     * @return array{label: string, from: string|null, to: string|null}|null
     */
    private function change(string $label, ?string $from, ?string $to): ?array
    {
        if ($from === $to) {
            return null;
        }

        return ['label' => $label, 'from' => $from, 'to' => $to];
    }

    private function update(): void
    {
        $this->loan->fill([
            'direction' => $this->direction,
            'status' => $this->status,
            'party' => $this->party,
            'purpose' => $this->purpose,
            'loaned_at' => $this->loanedAt,
            'due_at' => $this->dueAt,
            'returned_at' => $this->returnedAt,
            'item_condition_out_id' => $this->itemConditionOutId,
            'item_condition_in_id' => $this->itemConditionInId,
            'deposit_amount' => $this->depositAmount,
            'deposit_currency_code' => $this->depositAmount === null
                ? null
                : ($this->depositCurrencyCode ?? $this->loan->deposit_currency_code ?? $this->loan->copy->item->catalog->currency),
            'include_in_provenance' => $this->includeInProvenance,
        ]);
        $this->loan->updated_by_id = $this->user->id;
        $this->loan->updated_by_name = $this->user->getFullName();
        $this->loan->save();
    }

    /**
     * Keep the linked events in step with the flag: create the loan event, and
     * the return event once there is a return, when the loan is marked for
     * provenance; remove both when the mark is taken off.
     */
    private function reconcileProvenance(): void
    {
        if (! $this->includeInProvenance) {
            if ($this->loan->loan_provenance_event_id !== null || $this->loan->return_provenance_event_id !== null) {
                $this->removeLoanProvenance($this->loan);
            }

            return;
        }

        if ($this->loan->loan_provenance_event_id === null) {
            $this->createLoanProvenanceEvent($this->loan);
        }

        if ($this->loan->return_provenance_event_id === null) {
            $this->createReturnProvenanceEvent($this->loan);
        }
    }

    private function log(): void
    {
        $item = $this->loan->copy->item;

        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::LoanUpdate,
            parameters: ['name' => $item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $item,
            user: $this->user,
            action: ItemActionEnum::LoanUpdate,
            parameters: $this->changes === [] ? null : ['changes' => $this->changes],
        )->onQueue('low');
    }
}
