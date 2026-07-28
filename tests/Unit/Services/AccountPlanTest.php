<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Catalog;
use App\Models\Item;
use App\Services\AccountPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Fill an account with the given number of items, all in one collection.
 */
function fillAccountWithItems(int $count): Account
{
    $account = test()->createAccount();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    Item::factory()->count($count)->create(['catalog_id' => $catalog->id]);

    return $account;
}

it('does not limit a self hosted instance', function () {
    config(['pricing.hosted' => false]);

    $plan = new AccountPlan(account: fillAccountWithItems(20));

    expect($plan->isLimited())->toBeFalse();
    expect($plan->isOverFreeLimit())->toBeFalse();
    expect($plan->hasReachedHardLimit())->toBeFalse();
});

it('does not limit an account that has been unlocked', function () {
    config(['pricing.hosted' => true]);

    $account = fillAccountWithItems(20);
    $account->update(['unlocked_at' => now()]);

    $plan = new AccountPlan(account: $account);

    expect($plan->isLimited())->toBeFalse();
    expect($plan->hasReachedHardLimit())->toBeFalse();
});

it('counts items across every collection of the account', function () {
    config(['pricing.hosted' => true]);

    $account = test()->createAccount();
    $first = Catalog::factory()->create(['account_id' => $account->id]);
    $second = Catalog::factory()->create(['account_id' => $account->id]);
    Item::factory()->count(4)->create(['catalog_id' => $first->id]);
    Item::factory()->count(3)->create(['catalog_id' => $second->id]);

    expect(new AccountPlan(account: $account)->itemsUsed())->toBe(7);
});

it('leaves an account inside the free allowance alone', function () {
    config(['pricing.hosted' => true]);

    $plan = new AccountPlan(account: fillAccountWithItems(10));

    expect($plan->isLimited())->toBeTrue();
    expect($plan->isOverFreeLimit())->toBeFalse();
    expect($plan->hasReachedHardLimit())->toBeFalse();
    expect($plan->itemsOverLimit())->toBe(0);
    expect($plan->itemsRemaining())->toBe(5);
});

it('reports an account inside the grace as over the free limit but still growing', function () {
    config(['pricing.hosted' => true]);

    $plan = new AccountPlan(account: fillAccountWithItems(11));

    expect($plan->isOverFreeLimit())->toBeTrue();
    expect($plan->hasReachedHardLimit())->toBeFalse();
    expect($plan->itemsOverLimit())->toBe(1);
    expect($plan->itemsRemaining())->toBe(4);
});

it('stops an account that has used the whole grace', function () {
    config(['pricing.hosted' => true]);

    $plan = new AccountPlan(account: fillAccountWithItems(15));

    expect($plan->isOverFreeLimit())->toBeTrue();
    expect($plan->hasReachedHardLimit())->toBeTrue();
    expect($plan->itemsOverLimit())->toBe(5);
    expect($plan->itemsRemaining())->toBe(0);
});

it('keeps reporting the hard limit once an account is past it', function () {
    config(['pricing.hosted' => true]);

    $plan = new AccountPlan(account: fillAccountWithItems(16));

    expect($plan->hasReachedHardLimit())->toBeTrue();
    expect($plan->itemsRemaining())->toBe(0);
});

it('reads the allowance from the configuration', function () {
    config(['pricing.hosted' => true, 'pricing.free_item_limit' => 3, 'pricing.grace_items' => 1]);

    $plan = new AccountPlan(account: fillAccountWithItems(3));

    expect($plan->freeLimit())->toBe(3);
    expect($plan->hardLimit())->toBe(4);
    expect($plan->hasReachedHardLimit())->toBeFalse();
});
