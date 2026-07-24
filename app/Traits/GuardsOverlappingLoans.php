<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Models\Copy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

/**
 * A physical copy can only be in one place at a time, so it may have at most one
 * open outgoing loan. This guard rejects a second one, keeping the rule in the
 * Action so the web app and the JSON API enforce it the same way.
 */
trait GuardsOverlappingLoans
{
    /**
     * Reject an open outgoing loan on a copy that already has one. Only outgoing,
     * open loans compete for custody, so nothing else is blocked. The loan being
     * edited is ignored so re-saving it does not clash with itself.
     */
    private function guardAgainstOverlappingLoan(
        Copy $copy,
        LoanDirection $direction,
        LoanStatus $status,
        ?int $ignoreLoanId = null,
    ): void {
        if ($direction !== LoanDirection::Outgoing || ! $status->isOpen()) {
            return;
        }

        $clashes = $copy->loans()
            ->where('direction', LoanDirection::Outgoing)
            ->whereIn('status', LoanStatus::openCases())
            ->when($ignoreLoanId !== null, fn (Builder $query): Builder => $query->whereKeyNot($ignoreLoanId))
            ->exists();

        if (! $clashes) {
            return;
        }

        throw ValidationException::withMessages([
            'copy' => __('This copy already has an open outgoing loan. Return the current loan before lending it out again.'),
        ]);
    }
}
