<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * How much weight a valuation carries.
 */
enum ValuationConfidence: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Low => __('Low'),
            self::Medium => __('Medium'),
            self::High => __('High'),
            self::Unknown => __('Unknown'),
        };
    }

    /**
     * The colour of the dot that stands next to the confidence on a valuation,
     * green for a figure that can be trusted and amber for one that cannot.
     */
    public function color(): string
    {
        return match ($this) {
            self::High => '#34d399',
            self::Medium => '#3b82f6',
            self::Low => '#f59e0b',
            self::Unknown => '#94a3b8',
        };
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
