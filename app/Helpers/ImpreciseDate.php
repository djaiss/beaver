<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Enums\DatePrecision;
use Carbon\Carbon;

/**
 * Renders a date against how much of it is actually known.
 *
 * A provenance record often says only that something happened in 1987, or
 * roughly then. The date is stored in full because a column has to hold
 * something, but showing the stored day would claim a precision the evidence
 * does not support. This reads the date back at the precision it was recorded
 * at, so a year-precision date renders as "1987" rather than "1 January 1987".
 */
class ImpreciseDate
{
    public static function format(?Carbon $date, DatePrecision $precision): string
    {
        // Unknown wins over whatever happens to sit in the column: an event
        // recorded as undated must not start reading as dated because a date was
        // left behind by an earlier edit.
        if ($precision === DatePrecision::Unknown || ! $date instanceof Carbon) {
            return __('Date unknown');
        }

        // Unknown is handled by the guard above, so it cannot reach the match.
        return match ($precision) {
            DatePrecision::Exact => $date->isoFormat('LL'),
            DatePrecision::Month => $date->isoFormat('MMMM YYYY'),
            DatePrecision::Year => $date->isoFormat('YYYY'),
            DatePrecision::Approximate => __('circa :year', ['year' => $date->isoFormat('YYYY')]),
        };
    }

    /**
     * A short form for a timeline, where the entries line up in a narrow column
     * and the full date would crowd the entry beside it.
     */
    public static function short(?Carbon $date, DatePrecision $precision): string
    {
        if ($precision === DatePrecision::Unknown || ! $date instanceof Carbon) {
            return '—';
        }

        // Unknown is handled by the guard above, so it cannot reach the match.
        return match ($precision) {
            DatePrecision::Exact => $date->isoFormat('ll'),
            DatePrecision::Month => $date->isoFormat('MMM YYYY'),
            DatePrecision::Year => $date->isoFormat('YYYY'),
            DatePrecision::Approximate => __('c. :year', ['year' => $date->isoFormat('YYYY')]),
        };
    }
}
