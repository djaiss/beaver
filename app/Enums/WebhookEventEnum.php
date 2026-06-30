<?php

declare(strict_types=1);

namespace App\Enums;

enum WebhookEventEnum: string
{
    case VaultCreated = 'vault.created';
    case VaultDestroyed = 'vault.destroyed';
}
