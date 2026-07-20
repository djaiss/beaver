<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * What kind of work a maintenance record logs.
 *
 * Most of these are routine care that stays in this model. A conservation or a
 * restoration can also be significant enough to belong to the object's story, in
 * which case the record is marked to generate a matching provenance event.
 */
enum MaintenanceType: string
{
    case Cleaning = 'cleaning';
    case Repair = 'repair';
    case Servicing = 'servicing';
    case Conservation = 'conservation';
    case Restoration = 'restoration';
    case Replacement = 'replacement';
    case Inspection = 'inspection';

    public function label(): string
    {
        return match ($this) {
            self::Cleaning => __('Cleaning'),
            self::Repair => __('Repair'),
            self::Servicing => __('Servicing'),
            self::Conservation => __('Conservation'),
            self::Restoration => __('Restoration'),
            self::Replacement => __('Replacement'),
            self::Inspection => __('Inspection'),
        };
    }

    /**
     * The colour of the type badge on the record.
     */
    public function color(): string
    {
        return match ($this) {
            self::Cleaning => '#38bdf8',
            self::Repair => '#f59e0b',
            self::Servicing => '#8b5cf6',
            self::Conservation => '#10b981',
            self::Restoration => '#ec4899',
            self::Replacement => '#f97316',
            self::Inspection => '#64748b',
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
