<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\PermissionEnum;
use App\Models\Member;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createVault(string $name = 'New York Public Library'): Vault
    {
        return Vault::factory()->create([
            'name' => $name,
        ]);
    }

    protected function assignUserToVault(User $user, Vault $vault, string $role = PermissionEnum::Viewer->value): Member
    {
        return Member::factory()->create([
            'user_id' => $user->id,
            'vault_id' => $vault->id,
            'role' => $role,
        ]);
    }
}
