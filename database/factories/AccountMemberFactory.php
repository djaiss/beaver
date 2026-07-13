<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountMember>
 */
class AccountMemberFactory extends Factory
{
    protected $model = AccountMember::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'user_id' => User::factory(),
            'role' => PermissionEnum::Viewer->value,
            'invited_by' => null,
            'joined_at' => now(),
        ];
    }
}
