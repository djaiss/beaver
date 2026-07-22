<?php

declare(strict_types=1);
use App\Enums\LoanStatus;
use App\Jobs\FlagOverdueLoans;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('flips active loans past their due date to overdue', function () {
    $loan = Loan::factory()->create([
        'status' => LoanStatus::Active,
        'due_at' => now()->subDay()->toDateString(),
    ]);

    new FlagOverdueLoans()->handle();

    expect($loan->refresh()->status)->toBe(LoanStatus::Overdue);
});

it('leaves an active loan not yet due alone', function () {
    $loan = Loan::factory()->create([
        'status' => LoanStatus::Active,
        'due_at' => now()->addDays(10)->toDateString(),
    ]);

    new FlagOverdueLoans()->handle();

    expect($loan->refresh()->status)->toBe(LoanStatus::Active);
});

it('leaves an open ended active loan alone', function () {
    $loan = Loan::factory()->create([
        'status' => LoanStatus::Active,
        'due_at' => null,
    ]);

    new FlagOverdueLoans()->handle();

    expect($loan->refresh()->status)->toBe(LoanStatus::Active);
});

it('does not touch loans that are not active', function () {
    $planned = Loan::factory()->create(['status' => LoanStatus::Planned, 'due_at' => now()->subDay()->toDateString()]);
    $returned = Loan::factory()->create(['status' => LoanStatus::Returned, 'due_at' => now()->subDay()->toDateString()]);

    new FlagOverdueLoans()->handle();

    expect($planned->refresh()->status)->toBe(LoanStatus::Planned);
    expect($returned->refresh()->status)->toBe(LoanStatus::Returned);
});
