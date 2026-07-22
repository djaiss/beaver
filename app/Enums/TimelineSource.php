<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Which model a unified-history entry was assembled from.
 *
 * The timeline is a read model: nothing is copied into a history table, so every
 * entry has to say which source it came from. That source drives the colour of
 * its dot, the label on its pill, and the history section it links back into, so
 * the reader can tell an appraisal from a purchase at a glance.
 */
enum TimelineSource: string
{
    case Transaction = 'transaction';
    case Provenance = 'provenance';
    case Valuation = 'valuation';
    case Insurance = 'insurance';
    case Maintenance = 'maintenance';
    case Loan = 'loan';
    case Location = 'location';

    public function label(): string
    {
        return match ($this) {
            self::Transaction => __('Transaction'),
            self::Provenance => __('Provenance'),
            self::Valuation => __('Valuation'),
            self::Insurance => __('Insurance'),
            self::Maintenance => __('Maintenance'),
            self::Loan => __('Loan'),
            self::Location => __('Location'),
        };
    }

    /**
     * The colour of the entry's dot and pill, matching the section it came from.
     */
    public function color(): string
    {
        return match ($this) {
            self::Transaction => '#34d399',
            self::Provenance => '#6366f1',
            self::Valuation => '#3b82f6',
            self::Insurance => '#8b5cf6',
            self::Maintenance => '#f59e0b',
            self::Loan => '#ec4899',
            self::Location => '#14b8a6',
        };
    }

    /**
     * The history section this entry links back into, so the reader can open the
     * full record it was drawn from.
     */
    public function section(): string
    {
        return match ($this) {
            self::Transaction => 'transactions',
            self::Provenance => 'provenance',
            self::Valuation => 'valuations',
            self::Insurance => 'insurance',
            self::Maintenance => 'maintenance',
            self::Loan => 'loans',
            self::Location => 'locations',
        };
    }
}
