<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\LoanStatus;
use App\Models\Loan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FlagOverdueLoans implements ShouldQueue
{
    use Queueable;

    /**
     * Flip active loans to overdue once their due date has passed.
     *
     * Overdue is reached by the passage of time rather than set by hand, so this
     * is what makes the overdue state on the loans panel true. An outgoing loan
     * stays out under either status, so the copy keeps reading as loaned and its
     * status needs no touch here.
     */
    public function handle(): void
    {
        Loan::query()
            ->where('status', LoanStatus::Active->value)
            ->whereNotNull('due_at')
            ->whereDate('due_at', '<', now()->toDateString())
            ->update(['status' => LoanStatus::Overdue->value]);
    }
}
