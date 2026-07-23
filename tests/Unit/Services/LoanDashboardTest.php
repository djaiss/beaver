<?php

declare(strict_types=1);

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Loan;
use App\Services\LoanDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/**
 * Build a copy under an account, optionally in a named collection.
 */
function loanCopy(int $accountId, ?string $collectionName = null): Copy
{
    $collection = Collection::factory()->create(array_filter([
        'account_id' => $accountId,
        'name' => $collectionName,
    ]));
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('counts the stat tiles for the direction', function () {
    $user = $this->createUser();

    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'due_at' => null]);
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Planned]);
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Returned]);
    // An incoming loan must not leak into the outgoing direction.
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Incoming, 'status' => LoanStatus::Active]);

    $tiles = new LoanDashboard($user->account, LoanDirection::Outgoing)->statTiles();

    expect($tiles['active'])->toBe(1)
        ->and($tiles['planned'])->toBe(1)
        ->and($tiles['returned'])->toBe(1);
});

it('does not include another account\'s loans', function () {
    $user = $this->createUser();
    $other = $this->createAccount();

    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);
    Loan::factory()->create(['copy_id' => loanCopy($other->id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);

    $all = new LoanDashboard($user->account, LoanDirection::Outgoing)->filtered();

    expect($all)->toHaveCount(1);
});

it('splits the due groups into overdue, due soon, and open ended', function () {
    $user = $this->createUser();

    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'due_at' => Carbon::today()->subDays(3)]);
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'due_at' => Carbon::today()->addDays(10)]);
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'due_at' => null]);
    // Far in the future: not due soon.
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'due_at' => Carbon::today()->addDays(90)]);

    $groups = new LoanDashboard($user->account, LoanDirection::Outgoing)->dueGroups();

    expect($groups['overdue'])->toHaveCount(1)
        ->and($groups['dueSoon'])->toHaveCount(1)
        ->and($groups['openEnded'])->toHaveCount(1);
});

it('flags a loan returned in a worse condition as a risk', function () {
    $user = $this->createUser();
    $good = ItemCondition::factory()->create(['account_id' => $user->account_id, 'position' => 1]);
    $poor = ItemCondition::factory()->create(['account_id' => $user->account_id, 'position' => 5]);

    Loan::factory()->create([
        'copy_id' => loanCopy($user->account_id)->id,
        'direction' => LoanDirection::Outgoing,
        'status' => LoanStatus::Returned,
        'item_condition_out_id' => $good->id,
        'item_condition_in_id' => $poor->id,
    ]);

    $risk = new LoanDashboard($user->account, LoanDirection::Outgoing)->riskGroups();

    expect($risk['returnedWorse'])->toHaveCount(1);
});

it('sums open loan deposits by currency', function () {
    $user = $this->createUser();

    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'deposit_amount' => 20000, 'deposit_currency_code' => 'USD']);
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'deposit_amount' => 5000, 'deposit_currency_code' => 'USD']);
    // A returned loan's deposit is no longer held.
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Returned, 'deposit_amount' => 9999, 'deposit_currency_code' => 'USD']);

    $deposits = new LoanDashboard($user->account, LoanDirection::Outgoing)->depositsData();

    expect($deposits['totals']['USD'])->toBe(25000)
        ->and($deposits['count'])->toBe(3);
});

it('groups loans by party, most active first', function () {
    $user = $this->createUser();

    Loan::factory()->count(2)->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'party' => 'The Whitney']);
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Returned, 'party' => 'A friend']);

    $parties = new LoanDashboard($user->account, LoanDirection::Outgoing)->parties();

    expect($parties)->toHaveCount(2)
        ->and($parties->first()['name'])->toBe('The Whitney')
        ->and($parties->first()['active'])->toBe(2);
});

it('searches across party, item, and copy identifier', function () {
    $user = $this->createUser();
    $copy = loanCopy($user->account_id);
    $copy->update(['identifier' => 'CBX-42']);

    Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'party' => 'The Whitney']);
    Loan::factory()->create(['copy_id' => loanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'party' => 'Someone else']);

    $found = new LoanDashboard($user->account, LoanDirection::Outgoing)->filtered(search: 'cbx-42');

    expect($found)->toHaveCount(1);
});
