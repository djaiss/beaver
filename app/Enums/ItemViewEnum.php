<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemViewEnum: string
{
    case Grid = 'grid';
    case List = 'list';
    case Table = 'table';

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
