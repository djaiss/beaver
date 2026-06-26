<?php

declare(strict_types=1);

namespace App\ViewModels\Settings;

use App\Models\User;
use Illuminate\Support\Collection;

class AccountIndexViewModel
{
    public function __construct(
        private readonly User $user,
    ) {}

    public function vaultsToDelete(): Collection
    {
        return $this->user
            ->memberships()
            ->where('role', 'owner')
            ->with('vault')
            ->get()
            ->filter(fn ($membership): bool => $membership
                ->vault
                ->members()
                ->where('role', 'owner')
                ->count() === 1)
            ->map(fn ($membership) => (object) [
                'name' => $membership->vault->name,
                'link' => route('vault.show', $membership->vault->id),
                'avatar' => $membership->vault->getAvatar(),
            ]);
    }

    public function vaultsNotDeleted(): Collection
    {
        return $this->user
            ->memberships()
            ->where('role', 'owner')
            ->with('vault')
            ->get()
            ->filter(fn ($membership): bool => $membership
                ->vault
                ->members()
                ->where('role', 'owner')
                ->count() > 1)
            ->map(fn ($membership) => (object) [
                'name' => $membership->vault->name,
                'link' => route('vault.show', $membership->vault->id),
                'avatar' => $membership->vault->getAvatar(),
            ]);
    }

    public function url(): object
    {
        return (object) [
            'dashboard' => route('vault.index'),
            'settings' => route('settings.index'),
            'deleteAccount' => route('settings.account.destroy'),
        ];
    }
}
