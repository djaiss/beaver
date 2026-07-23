<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where a loan sits in its lifecycle.
 *
 * A loan is planned before the object moves, active while it is out, and overdue
 * once its due date has passed without a return. It closes as returned when the
 * object comes back, cancelled when a planned loan never happens, or lost when
 * the object does not come back.
 *
 * Overdue is reached by the passage of time rather than set by hand: a scheduled
 * job flips active loans to overdue once their due date has passed. Returned is
 * reached through the return flow rather than edited in directly.
 */
enum LoanStatus: string
{
    case Planned = 'planned';
    case Active = 'active';
    case Overdue = 'overdue';
    case Returned = 'returned';
    case Cancelled = 'cancelled';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::Planned => __('Planned'),
            self::Active => __('Active'),
            self::Overdue => __('Overdue'),
            self::Returned => __('Returned'),
            self::Cancelled => __('Cancelled'),
            self::Lost => __('Lost'),
        };
    }

    /**
     * The badge colour the status shows as.
     *
     * An out object reads as positive, an overdue or lost one as an error, and a
     * closed or not-yet-started one as neutral.
     */
    public function color(): ?string
    {
        return match ($this) {
            self::Active => 'emerald',
            self::Planned => 'orange',
            self::Overdue, self::Lost => 'error',
            self::Returned, self::Cancelled => null,
        };
    }

    /**
     * Whether the loan is still open rather than closed: the object is out, or
     * planned to go out, as opposed to back, cancelled or written off.
     */
    public function isOpen(): bool
    {
        return in_array($this, [self::Planned, self::Active, self::Overdue], true);
    }

    /**
     * The statuses that count as open, for querying loans still in flight.
     *
     * @return list<self>
     */
    public static function openCases(): array
    {
        return [self::Planned, self::Active, self::Overdue];
    }

    /**
     * Whether the object has actually left custody under this status.
     *
     * A planned loan has not moved the object yet, so it does not count. Active
     * and overdue do: this is the test the copy state is kept in step with, so an
     * outgoing loan in one of these states reads the copy as loaned out.
     */
    public function hasLeftCustody(): bool
    {
        return in_array($this, [self::Active, self::Overdue], true);
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
