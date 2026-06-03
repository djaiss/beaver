<?php

declare(strict_types=1);

namespace App\Enums;

enum VaultActionEnum: string
{
    case VaultCreation = 'vault_creation';
    case GenderCreation = 'gender_creation';
    case GenderUpdate = 'gender_update';
    case GenderDeletion = 'gender_deletion';
}
