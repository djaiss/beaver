<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where a piece of insurance coverage stands.
 */
enum InsuranceStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('Active'),
            self::Expired => __('Expired'),
            self::Cancelled => __('Cancelled'),
            self::Pending => __('Pending'),
        };
    }

    /**
     * The colour of the status badge, green while the coverage holds and red once
     * it has been cancelled.
     */
    public function color(): string
    {
        return match ($this) {
            self::Active => '#34d399',
            self::Expired => '#64748b',
            self::Cancelled => '#ef4444',
            self::Pending => '#f59e0b',
        };
    }

    /**
     * Whether a record in this status should read second to the active one.
     *
     * Expired and cancelled coverage is history, so the panel dims it to let the
     * live record stand out.
     */
    public function isMuted(): bool
    {
        return $this === self::Expired || $this === self::Cancelled;
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
