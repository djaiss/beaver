<?php

declare(strict_types=1);

namespace App\ViewModels\Vault;

class VaultNewViewModel
{
    public function url(): object
    {
        return (object) [
            'vaultIndex' => route('vault.index'),
            'vaultCreate' => route('vault.create'),
        ];
    }
}
