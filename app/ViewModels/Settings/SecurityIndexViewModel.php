<?php

declare(strict_types=1);

namespace App\ViewModels\Settings;

use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\PersonalAccessToken;
use SensitiveParameter;

class SecurityIndexViewModel
{
    public function __construct(
        private readonly User $user,
    ) {}

    public function tokens(): Collection
    {
        return $this->user
            ->tokens->map(fn (#[SensitiveParameter] PersonalAccessToken $token) => (object) [
            'id' => $token->id,
            'name' => $token->name,
            'last_used' => $token->last_used_at ? $token->last_used_at->diffForHumans() : trans('Never'),
            'just_added' => false,
            'token' => $token->token,
            'url' => route('settings.api-keys.destroy', $token->id),
        ]);
    }

    public function has2fa(): bool
    {
        return $this->user->two_factor_confirmed_at !== null;
    }

    public function url(): object
    {
        return (object) [
            'vault' => route('vault.index'),
            'updatePassword' => route('settings.security.password.update'),
            'new2fa' => route('settings.security.2fa.new'),
            'destroy2fa' => route('settings.security.2fa.destroy'),
            'showRecoveryCodes' => route('settings.security.recoverycodes.show'),
            'updateAutoDelete' => route('settings.security.auto-delete.update'),
            'createApiKey' => route('settings.api-keys.create'),
        ];
    }
}
