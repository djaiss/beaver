<?php

declare(strict_types=1);

namespace App\Enums;

enum VisibilityEnum: string
{
    case Private = 'private';
    case Shared = 'shared';
    case Public = 'public';
}
