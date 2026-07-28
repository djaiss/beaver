<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;

/**
 * What an account is allowed to hold, and how much of it it has used.
 *
 * Only the managed instance limits anything: a self hosted one runs the same
 * code with no ceiling at all, which is what the MIT licence is for. On the
 * managed instance an account holds ten items for free, five more as a grace,
 * and then stops growing until it is unlocked by a one time payment.
 *
 * Every screen and App\Actions\CreateItem read the account's standing through
 * this class, so the rule is written down once.
 */
class AccountPlan
{
    public function __construct(
        private readonly Account $account,
    ) {}

    /**
     * Whether the free allowance applies at all.
     */
    public function isLimited(): bool
    {
        return config('pricing.hosted') === true && $this->account->unlocked_at === null;
    }

    public function itemsUsed(): int
    {
        return $this->account->items()->count();
    }

    public function freeLimit(): int
    {
        return (int) config('pricing.free_item_limit');
    }

    /**
     * The free allowance plus the grace, past which nothing more is accepted.
     */
    public function hardLimit(): int
    {
        return $this->freeLimit() + (int) config('pricing.grace_items');
    }

    /**
     * The account has outgrown the free allowance but is still inside the
     * grace, so it keeps accepting items while every screen says so.
     */
    public function isOverFreeLimit(): bool
    {
        return $this->isLimited() && $this->itemsUsed() > $this->freeLimit();
    }

    public function hasReachedHardLimit(): bool
    {
        return $this->isLimited() && $this->itemsUsed() >= $this->hardLimit();
    }

    /**
     * How many items sit above the free allowance, never below zero.
     */
    public function itemsOverLimit(): int
    {
        return max(0, $this->itemsUsed() - $this->freeLimit());
    }

    /**
     * How many items can still be added before the account stops growing.
     */
    public function itemsRemaining(): int
    {
        return max(0, $this->hardLimit() - $this->itemsUsed());
    }
}
