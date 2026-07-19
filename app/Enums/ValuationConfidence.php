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
}
