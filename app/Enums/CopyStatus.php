<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where a copy sits in its lifecycle.
 *
 * A copy is a current-state record, and this is the part of that state which
 * says whether the account still holds the object, is waiting on it, or has
 * parted with it. The history behind the status lives in the models hanging off
 * the copy, not here.
 */
enum CopyStatus: string
{
    case Owned = 'owned';
    case Ordered = 'ordered';
    case Loaned = 'loaned';
    case Sold = 'sold';
    case Gifted = 'gifted';
    case Lost = 'lost';
    case Stolen = 'stolen';
    case Disposed = 'disposed';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Owned => __('Owned'),
            self::Ordered => __('Ordered'),
            self::Loaned => __('Loaned out'),
            self::Sold => __('Sold'),
            self::Gifted => __('Gifted'),
            self::Lost => __('Lost'),
            self::Stolen => __('Stolen'),
            self::Disposed => __('Disposed'),
            self::Other => __('Other'),
        };
    }

    /**
     * The badge colour the status shows as.
     *
     * The statuses that mean the object is still held read as positive, the
     * ones that mean it was lost against the account's will read as an error,
     * and a deliberate parting reads as neutral.
     */
    public function color(): ?string
    {
        return match ($this) {
            self::Owned => 'emerald',
            self::Ordered => 'orange',
            self::Loaned => 'pink',
            self::Gifted => 'violet',
            self::Lost, self::Stolen => 'error',
            self::Sold, self::Disposed, self::Other => null,
        };
    }

    /**
     * Whether the account still physically holds the copy.
     *
     * A loaned copy counts as held: custody moved, ownership did not.
     */
    public function isHeld(): bool
    {
        return in_array($this, [self::Owned, self::Ordered, self::Loaned], true);
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
