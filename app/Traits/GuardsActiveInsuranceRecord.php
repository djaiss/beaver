<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\InsuranceStatus;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use Illuminate\Validation\ValidationException;

/**
 * The rule that a copy holds at most one active record per policy.
 *
 * Two live records for the same policy would each claim to be the current
 * coverage, so recording or reviving one is refused while another active record
 * on the copy carries the same policy number. A record with no policy number is
 * left alone: without a number there is no policy to be the same as.
 *
 * The policy number is encrypted, so the comparison cannot run in the database
 * and the active records are read out and compared in memory instead.
 */
trait GuardsActiveInsuranceRecord
{
    private function guardAgainstSecondActiveRecord(
        Copy $copy,
        InsuranceStatus $status,
        ?string $policyNumber,
        ?int $ignoreId = null,
    ): void {
        if ($status !== InsuranceStatus::Active || $policyNumber === null) {
            return;
        }

        $clashes = $copy->insuranceRecords()
            ->where('status', InsuranceStatus::Active->value)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->get()
            ->contains(fn (InsuranceRecord $record): bool => $record->policy_number === $policyNumber);

        if ($clashes) {
            throw ValidationException::withMessages([
                'status' => __('Another active insurance record already covers this copy under that policy number.'),
            ]);
        }
    }
}
