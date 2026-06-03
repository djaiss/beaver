<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionEnum: string
{
    case Viewer = 'viewer';
    case Editor = 'editor';
    case Owner = 'owner';
}
