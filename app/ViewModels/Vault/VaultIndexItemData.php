<?php

declare(strict_types=1);

namespace App\ViewModels\Vault;

class VaultIndexItemData
{
    public function __construct(
        public string $name,
        public string $url,
        public string $avatar,
    ) {}
}
