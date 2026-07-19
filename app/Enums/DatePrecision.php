<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * How much of a date is actually known.
 *
 * Provenance dates are frequently uncertain: a record may say only that
 * something happened in 1987, or roughly then. Storing a full date and
 * rendering it as though the day were known would invent a precision the
 * evidence does not support, so the date carries how much of it to believe.
 */
enum DatePrecision: string
{
    case Exact = 'exact';
    case Month = 'month';
    case Year = 'year';
    case Approximate = 'approximate';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Exact => __('Exact date'),
            self::Month => __('Month'),
            self::Year => __('Year'),
            self::Approximate => __('Approximate'),
            self::Unknown => __('Unknown'),
        };
    }

    /**
     * What this precision means, shown under the date field.
     */
    public function hint(): string
    {
        return match ($this) {
            self::Exact => __('The full day is known.'),
            self::Month => __('Only the month and the year are known.'),
            self::Year => __('Only the year is known.'),
            self::Approximate => __('A best estimate, read it as circa.'),
            self::Unknown => __('The date is unknown, so no date is recorded.'),
        };
    }

    /**
     * Whether a date is worth recording at all against this precision.
     */
    public function carriesDate(): bool
    {
        return $this !== self::Unknown;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
