<?php

declare(strict_types=1);

namespace App\Enums;

enum FieldTypeEnum: string
{
    case Text = 'text';
    case Number = 'number';
    case Date = 'date';
    case Boolean = 'boolean';
    case Select = 'select';
    case Rating = 'rating';

    /**
     * The highest star a rating field accepts.
     */
    public const int MAX_RATING = 5;
}
