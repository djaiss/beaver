<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * A block of the dashboard a member can hide.
 *
 * The case values are persisted on the user and travel to the customize menu,
 * so they read as the words the section is known by rather than as the internal
 * names of the things behind them.
 */
enum DashboardSectionEnum: string
{
    case Summary = 'summary';
    case Additions = 'additions';
    case Collections = 'collections';
    case Loans = 'loans';
    case Locations = 'locations';
    case Activity = 'activity';

    public function label(): string
    {
        return match ($this) {
            self::Summary => __('Portfolio summary'),
            self::Additions => __('Recent additions'),
            self::Collections => __('Your collections'),
            self::Loans => __('Loan snapshot'),
            self::Locations => __('Where things are'),
            self::Activity => __('Account activity'),
        };
    }

    /**
     * The case values, for validation rules.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case): string => $case->value, self::cases());
    }
}
