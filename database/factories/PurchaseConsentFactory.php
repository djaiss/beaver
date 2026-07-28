<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PurchaseConsentChoice;
use App\Models\Account;
use App\Models\PurchaseConsent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseConsent>
 */
class PurchaseConsentFactory extends Factory
{
    protected $model = PurchaseConsent::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'user_id' => User::factory(),
            'user_name' => fake()->name(),
            'choice' => PurchaseConsentChoice::NonRefundable,
            'ip_address' => fake()->ipv4(),
            'accepted_at' => now(),
        ];
    }
}
