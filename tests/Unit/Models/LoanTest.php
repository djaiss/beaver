<?php

declare(strict_types=1);
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Loan;
use App\Models\ProvenanceEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a copy', function () {
    $copy = Copy::factory()->create();
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);

    expect($loan->copy)->toBeInstanceOf(Copy::class);
    expect($loan->copy->id)->toBe($copy->id);
});

it('belongs to a condition out and in', function () {
    $out = Condition::factory()->create();
    $in = Condition::factory()->create();
    $loan = Loan::factory()->create([
        'condition_out_id' => $out->id,
        'condition_in_id' => $in->id,
    ]);

    expect($loan->conditionOut)->toBeInstanceOf(Condition::class);
    expect($loan->conditionOut->id)->toBe($out->id);
    expect($loan->conditionIn->id)->toBe($in->id);
});

it('belongs to the provenance events it generated', function () {
    $loanEvent = ProvenanceEvent::factory()->create();
    $returnEvent = ProvenanceEvent::factory()->create();
    $loan = Loan::factory()->create([
        'loan_provenance_event_id' => $loanEvent->id,
        'return_provenance_event_id' => $returnEvent->id,
    ]);

    expect($loan->loanProvenanceEvent->id)->toBe($loanEvent->id);
    expect($loan->returnProvenanceEvent->id)->toBe($returnEvent->id);
});

it('casts the direction and status to enums, the dates, the deposit and the flag', function () {
    $loan = Loan::factory()->create([
        'direction' => LoanDirection::Incoming,
        'status' => LoanStatus::Overdue,
        'loaned_at' => '2024-01-01',
        'due_at' => '2024-06-01',
        'returned_at' => '2024-07-01',
        'deposit_amount' => 250000,
        'include_in_provenance' => true,
    ]);

    expect($loan->direction)->toBe(LoanDirection::Incoming);
    expect($loan->status)->toBe(LoanStatus::Overdue);
    expect($loan->loaned_at->toDateString())->toBe('2024-01-01');
    expect($loan->due_at->toDateString())->toBe('2024-06-01');
    expect($loan->returned_at->toDateString())->toBe('2024-07-01');
    expect($loan->deposit_amount)->toBe(250000);
    expect($loan->include_in_provenance)->toBeTrue();
});

it('encrypts the party and the purpose', function () {
    $loan = Loan::factory()->create([
        'party' => 'The Whitney Museum',
        'purpose' => 'Retrospective exhibition.',
    ]);

    $raw = fn (string $column): string => DB::table('loans')->where('id', $loan->id)->value($column);

    $this->assertNotSame('The Whitney Museum', $raw('party'));
    expect(decrypt($raw('party'), false))->toBe('The Whitney Museum');
    expect(decrypt($raw('purpose'), false))->toBe('Retrospective exhibition.');
});

it('reads an outgoing active or overdue loan as outstanding', function () {
    $active = Loan::factory()->create(['direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);
    $overdue = Loan::factory()->create(['direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Overdue]);

    expect($active->isOutstanding())->toBeTrue();
    expect($overdue->isOutstanding())->toBeTrue();
    expect($overdue->isOverdue())->toBeTrue();
});

it('does not read a planned, returned or incoming loan as outstanding', function () {
    $planned = Loan::factory()->create(['direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Planned]);
    $returned = Loan::factory()->create(['direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Returned]);
    $incoming = Loan::factory()->create(['direction' => LoanDirection::Incoming, 'status' => LoanStatus::Active]);

    expect($planned->isOutstanding())->toBeFalse();
    expect($returned->isOutstanding())->toBeFalse();
    expect($incoming->isOutstanding())->toBeFalse();
});

it('reads its loans off a copy, most recent first', function () {
    $copy = Copy::factory()->create();
    $older = Loan::factory()->create(['copy_id' => $copy->id, 'loaned_at' => '2023-01-01']);
    $newer = Loan::factory()->create(['copy_id' => $copy->id, 'loaned_at' => '2024-01-01']);

    expect($copy->loans->pluck('id')->all())->toBe([$newer->id, $older->id]);
});

it('reads the outstanding outgoing loan as the active loan of a copy', function () {
    $copy = Copy::factory()->create();
    Loan::factory()->create(['copy_id' => $copy->id, 'status' => LoanStatus::Returned]);
    $active = Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);

    expect($copy->activeLoan)->not->toBeNull();
    expect($copy->activeLoan->id)->toBe($active->id);
});

it('has no active loan when none is outstanding', function () {
    $copy = Copy::factory()->create();
    Loan::factory()->create(['copy_id' => $copy->id, 'status' => LoanStatus::Returned]);
    Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Incoming, 'status' => LoanStatus::Active]);

    expect($copy->refresh()->activeLoan)->toBeNull();
});
