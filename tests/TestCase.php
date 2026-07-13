<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createAccount(string $name = 'Central Perk'): Account
    {
        return Account::factory()->create([
            'name' => $name,
        ]);
    }

    protected function assignUserToAccount(User $user, Account $account, string $role = PermissionEnum::Viewer->value): AccountMember
    {
        return AccountMember::factory()->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'role' => $role,
        ]);
    }
}
