<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Copy;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'copy_id' => Copy::factory(),
            'type' => TransactionType::Purchase,
            'counterparty' => null,
            'amount' => fake()->numberBetween(100, 500000),
            'currency_code' => 'USD',
            'tax_amount' => null,
            'fee_amount' => null,
            'shipping_amount' => null,
            'total_amount' => null,
            'occurred_at' => fake()->date(),
            'reference_number' => null,
            'source_url' => null,
            'note' => null,
        ];
    }
}
