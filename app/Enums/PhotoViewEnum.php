<?php

declare(strict_types=1);

namespace App\Enums;

enum PhotoViewEnum: string
{
    case Grid = 'grid';
    case ByItem = 'by-item';

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
