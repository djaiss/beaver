<?php

declare(strict_types=1);

namespace App\Traits;

use App\Actions\CreateProvenanceEvent;
use App\Enums\CopyStatus;
use App\Enums\DatePrecision;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\ProvenanceEventType;
use App\Models\Copy;
use App\Models\Loan;

/**
 * The two side effects a loan has beyond its own row: the copy's status, and the
 * provenance events an institutional loan generates.
 *
 * An active outgoing loan means the object is not in the account's physical
 * custody, so the copy reads as loaned out while one is outstanding and returns
 * to owned once none is. Custody is all that moves: ownership, and so the copy's
 * location, are left alone.
 *
 * A loan marked for provenance generates a matching event for the loan and, once
 * it comes back, another for the return, so an exhibition or an institutional
 * loan reads in the object's documented story. Informal personal loans carry no
 * flag and stay in loan history only.
 *
 * These read the property `$this->user` off the action, which every loan action
 * carries.
 */
trait SyncsLoanState
{
    /**
     * Bring the copy's status in line with its outstanding loans.
     *
     * Read after the loan has been written, so it reflects the change. A copy
     * with any outgoing loan out reads as loaned; one with none that was only
     * loaned out returns to owned. A copy the user has since marked sold, lost or
     * otherwise is left as they set it.
     */
    private function syncCopyStatus(Copy $copy): void
    {
        $hasOutstanding = $copy->loans()
            ->where('direction', LoanDirection::Outgoing->value)
            ->whereIn('status', [LoanStatus::Active->value, LoanStatus::Overdue->value])
            ->exists();

        if ($hasOutstanding && $copy->status !== CopyStatus::Loaned) {
            $copy->status = CopyStatus::Loaned;
            $copy->save();

            return;
        }

        if (! $hasOutstanding && $copy->status === CopyStatus::Loaned) {
            $copy->status = CopyStatus::Owned;
            $copy->save();
        }
    }

    /**
     * Generate the provenance event that records the loan itself.
     */
    private function createLoanProvenanceEvent(Loan $loan): void
    {
        $event = new CreateProvenanceEvent(
            user: $this->user,
            copy: $loan->copy,
            type: ProvenanceEventType::Loan,
            title: $this->loanEventTitle($loan),
            occurredAtPrecision: DatePrecision::Exact,
            occurredAt: $loan->loaned_at->toDateString(),
            description: $loan->purpose,
            fromParty: $loan->direction === LoanDirection::Incoming ? $loan->party : null,
            toParty: $loan->direction === LoanDirection::Outgoing ? $loan->party : null,
        )->execute();

        $loan->loan_provenance_event_id = $event->id;
        $loan->save();
    }

    /**
     * Generate the provenance event that records the return, once there is one.
     */
    private function createReturnProvenanceEvent(Loan $loan): void
    {
        if ($loan->returned_at === null) {
            return;
        }

        $event = new CreateProvenanceEvent(
            user: $this->user,
            copy: $loan->copy,
            type: ProvenanceEventType::Return,
            title: $this->returnEventTitle($loan),
            occurredAtPrecision: DatePrecision::Exact,
            occurredAt: $loan->returned_at->toDateString(),
            fromParty: $loan->direction === LoanDirection::Outgoing ? $loan->party : null,
            toParty: $loan->direction === LoanDirection::Incoming ? $loan->party : null,
        )->execute();

        $loan->return_provenance_event_id = $event->id;
        $loan->save();
    }

    /**
     * Remove both events the loan generated and forget them.
     *
     * The events were only there because of the loan, so they go with it rather
     * than being left orphaned in the object's story.
     */
    private function removeLoanProvenance(Loan $loan): void
    {
        $loan->loanProvenanceEvent?->delete();
        $loan->returnProvenanceEvent?->delete();
        $loan->loan_provenance_event_id = null;
        $loan->return_provenance_event_id = null;
        $loan->save();
    }

    private function loanEventTitle(Loan $loan): string
    {
        return $loan->direction === LoanDirection::Outgoing
            ? __('Loaned to :party', ['party' => $loan->party])
            : __('Borrowed from :party', ['party' => $loan->party]);
    }

    private function returnEventTitle(Loan $loan): string
    {
        return $loan->direction === LoanDirection::Outgoing
            ? __('Returned from :party', ['party' => $loan->party])
            : __('Returned to :party', ['party' => $loan->party]);
    }
}
