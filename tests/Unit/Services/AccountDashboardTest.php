<?php

declare(strict_types=1);

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\TransactionType;
use App\Enums\VisibilityEnum;
use App\Models\Account;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Loan;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\Valuation;
use App\Services\AccountDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;

uses(RefreshDatabase::class);

/**
 * A copy worth a given amount, which takes two rows rather than one column.
 */
function accountValuedCopy(array $attributes, int $amount): Copy
{
    $copy = Copy::factory()->create($attributes);

    Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => $amount,
        'valued_at' => '2026-01-01',
    ]);

    return $copy;
}

it('counts what the whole account holds and what it is worth', function (): void {
    $account = Account::factory()->create();
    $comics = Catalog::factory()->create(['account_id' => $account->id]);
    $vinyl = Catalog::factory()->create(['account_id' => $account->id]);

    $spiderMan = Item::factory()->create(['catalog_id' => $comics->id]);
    $kindOfBlue = Item::factory()->create(['catalog_id' => $vinyl->id]);

    accountValuedCopy(['item_id' => $spiderMan->id], 60000);
    accountValuedCopy(['item_id' => $spiderMan->id], 20000);
    accountValuedCopy(['item_id' => $kindOfBlue->id], 40000);

    $totals = new AccountDashboard(account: $account)->totals();

    expect($totals['collections'])->toBe(2)
        ->and($totals['items'])->toBe(2)
        ->and($totals['copies'])->toBe(3)
        ->and($totals['valuedCopies'])->toBe(3)
        ->and($totals['value'])->toBe(120000)
        ->and($totals['average'])->toBe(60000);
});

it('leaves another account out of every total', function (): void {
    $account = Account::factory()->create();
    $joeys = Account::factory()->create();

    $mine = Catalog::factory()->create(['account_id' => $account->id]);
    $theirs = Catalog::factory()->create(['account_id' => $joeys->id]);

    accountValuedCopy(['item_id' => Item::factory()->create(['catalog_id' => $mine->id])->id], 10000);
    accountValuedCopy(['item_id' => Item::factory()->create(['catalog_id' => $theirs->id])->id], 99000);

    $totals = new AccountDashboard(account: $account)->totals();

    expect($totals['collections'])->toBe(1)
        ->and($totals['items'])->toBe(1)
        ->and($totals['copies'])->toBe(1)
        ->and($totals['value'])->toBe(10000);
});

// A copy nobody has valued still counts as a copy, so the gap is visible rather
// than hidden behind a total that silently ignores it.
it('counts an unvalued copy without counting it as valued', function (): void {
    $account = Account::factory()->create();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    accountValuedCopy(['item_id' => $item->id], 30000);
    Copy::factory()->create(['item_id' => $item->id]);

    $totals = new AccountDashboard(account: $account)->totals();

    expect($totals['copies'])->toBe(2)
        ->and($totals['valuedCopies'])->toBe(1)
        ->and($totals['value'])->toBe(30000);
});

it('reports what was added this month', function (): void {
    Date::setTestNow('2026-06-15');

    $account = Account::factory()->create();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $old = Item::factory()->create(['catalog_id' => $catalog->id, 'created_at' => '2026-04-02']);
    $new = Item::factory()->create(['catalog_id' => $catalog->id, 'created_at' => '2026-06-02']);

    $oldCopy = accountValuedCopy(['item_id' => $old->id], 50000);
    $newCopy = accountValuedCopy(['item_id' => $new->id], 12000);

    Transaction::factory()->create(['copy_id' => $oldCopy->id, 'type' => TransactionType::Purchase, 'occurred_at' => '2026-04-02']);
    Transaction::factory()->create(['copy_id' => $newCopy->id, 'type' => TransactionType::Purchase, 'occurred_at' => '2026-06-02']);

    $totals = new AccountDashboard(account: $account)->totals();

    expect($totals['itemsAddedThisMonth'])->toBe(1)
        ->and($totals['valueAddedThisMonth'])->toBe(12000);
});

it('lists the collections with what each one holds', function (): void {
    $account = Account::factory()->create();
    $comics = Catalog::factory()->create([
        'account_id' => $account->id,
        'name' => 'Marvel Comics',
        'visibility' => VisibilityEnum::Shared,
    ]);

    $item = Item::factory()->create(['catalog_id' => $comics->id]);
    accountValuedCopy(['item_id' => $item->id], 25000);
    accountValuedCopy(['item_id' => $item->id], 15000);

    $collections = new AccountDashboard(account: $account)->collections();

    expect($collections)->toHaveCount(1)
        ->and($collections[0]['catalog']->name)->toBe('Marvel Comics')
        ->and($collections[0]['items'])->toBe(1)
        ->and($collections[0]['copies'])->toBe(2)
        ->and($collections[0]['value'])->toBe(40000);
});

it('lists the newest items first, with the condition and location of their first copy', function (): void {
    $account = Account::factory()->create();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id, 'name' => 'Box A1']);
    $condition = ItemCondition::factory()->create(['account_id' => $account->id, 'name' => 'Near Mint']);

    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'New Mutants #98', 'created_at' => '2026-06-01']);
    $newest = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #365', 'created_at' => '2026-06-10']);

    Copy::factory()->create([
        'item_id' => $newest->id,
        'current_location_id' => $location->id,
        'item_condition_id' => $condition->id,
    ]);

    $additions = new AccountDashboard(account: $account)->recentAdditions();

    expect($additions)->toHaveCount(2)
        ->and($additions[0]['item']->name)->toBe('Amazing Spider-Man #365')
        ->and($additions[0]['condition'])->toBe('Near Mint')
        ->and($additions[0]['location'])->toBe('Box A1')
        ->and($additions[0]['copies'])->toBe(1)
        ->and($additions[1]['item']->name)->toBe('New Mutants #98');
});

it('counts where custody stands in both directions', function (): void {
    Date::setTestNow('2026-06-15');

    $account = Account::factory()->create();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $lentOut = Copy::factory()->create(['item_id' => $item->id]);
    $borrowed = Copy::factory()->create(['item_id' => $item->id]);
    $late = Copy::factory()->create(['item_id' => $item->id]);
    $soon = Copy::factory()->create(['item_id' => $item->id]);

    Loan::factory()->create(['copy_id' => $lentOut->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'due_at' => null]);
    Loan::factory()->create(['copy_id' => $borrowed->id, 'direction' => LoanDirection::Incoming, 'status' => LoanStatus::Active, 'due_at' => null]);
    Loan::factory()->create(['copy_id' => $late->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'due_at' => '2026-06-01']);
    Loan::factory()->create(['copy_id' => $soon->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'due_at' => '2026-06-20']);

    $snapshot = new AccountDashboard(account: $account)->loanSnapshot();

    expect($snapshot['outgoing'])->toBe(3)
        ->and($snapshot['incoming'])->toBe(1)
        ->and($snapshot['overdue'])->toBe(1)
        ->and($snapshot['dueSoon'])->toBe(1);
});

it('sums the deposits held across open loans by currency', function (): void {
    $account = Account::factory()->create();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    Loan::factory()->create([
        'copy_id' => Copy::factory()->create(['item_id' => $item->id])->id,
        'status' => LoanStatus::Active,
        'deposit_amount' => 50000,
        'deposit_currency_code' => 'USD',
    ]);

    Loan::factory()->create([
        'copy_id' => Copy::factory()->create(['item_id' => $item->id])->id,
        'status' => LoanStatus::Returned,
        'deposit_amount' => 90000,
        'deposit_currency_code' => 'USD',
    ]);

    $snapshot = new AccountDashboard(account: $account)->loanSnapshot();

    expect($snapshot['deposits'])->toBe(['USD' => 50000]);
});

it('ranks the locations by the value they hold', function (): void {
    $account = Account::factory()->create();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $displayCase = Location::factory()->create(['account_id' => $account->id, 'name' => 'Display Case']);
    $shelf = Location::factory()->create(['account_id' => $account->id, 'name' => 'Shelf 3']);

    accountValuedCopy(['item_id' => $item->id, 'current_location_id' => $displayCase->id], 68000);
    accountValuedCopy(['item_id' => $item->id, 'current_location_id' => $shelf->id], 23000);

    $locations = new AccountDashboard(account: $account)->valueByLocation();

    expect($locations)->toBe([
        ['label' => 'Display Case', 'value' => 68000],
        ['label' => 'Shelf 3', 'value' => 23000],
    ]);
});

it('leaves a deleted collection out of the totals', function (): void {
    $account = Account::factory()->create();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    accountValuedCopy(['item_id' => $item->id], 10000);

    $catalog->delete();

    $totals = new AccountDashboard(account: $account)->totals();

    expect($totals['collections'])->toBe(0)
        ->and($totals['items'])->toBe(0)
        ->and($totals['copies'])->toBe(0)
        ->and($totals['value'])->toBe(0);
});
