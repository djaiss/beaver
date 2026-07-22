<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Models\Copy;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'copy_id' => Copy::factory(),
            'loan_provenance_event_id' => null,
            'return_provenance_event_id' => null,
            'direction' => LoanDirection::Outgoing,
            'status' => LoanStatus::Active,
            'party' => fake()->company(),
            'purpose' => null,
            'loaned_at' => fake()->date(),
            'due_at' => null,
            'returned_at' => null,
            'item_condition_out_id' => null,
            'item_condition_in_id' => null,
            'deposit_amount' => null,
            'deposit_currency_code' => null,
            'include_in_provenance' => false,
        ];
    }
}
