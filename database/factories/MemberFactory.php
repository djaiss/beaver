<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PermissionEnum;
use App\Models\Member;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vault_id' => Vault::factory(),
            'user_id' => User::factory(),
            'last_person_seen_id' => null,
            'role' => PermissionEnum::Viewer->value,
            'timezone' => fake()->timezone(),
            'joined_at' => now(),
        ];
    }
}
