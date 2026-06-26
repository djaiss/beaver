<?php

declare(strict_types=1);

namespace App\ViewModels\Vault;

use App\Models\User;
use App\Models\Vault;
use Illuminate\Support\Collection;

class VaultIndexViewModel
{
    public function __construct(
        private readonly User $user,
    ) {}

    /**
     * @return Collection<int, VaultIndexItemData>
     */
    public function vaults(): Collection
    {
        return $this->user
            ->vaults()
            ->select(['vaults.id', 'vaults.name'])
            ->get()
            ->map(fn (Vault $vault): VaultIndexItemData => new VaultIndexItemData(
                name: $vault->name,
                url: route('vault.show', $vault),
                avatar: $vault->getAvatar(),
            ));
    }

    public function url(): object
    {
        return (object) [
            'create' => route('vault.new'),
            'join' => route('vault.join.create'),
        ];
    }
}
