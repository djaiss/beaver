<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Collection;

/**
 * The checklist on the getting started screen.
 *
 * Every step reads its state from the account rather than being ticked by hand,
 * so the list cannot claim something was done that was not.
 *
 * This answers only what is done and where each step leads. The wording lives in
 * the view, which is where the translation extractor looks for it.
 *
 * Two of the steps need care. A new account is seeded with 12 collection types
 * and 5 locations by PopulateAccount, so "does the account have any" would be
 * true from the first second and the step would never be actionable. The seeded
 * rows carry no author, while everything a user creates is stamped by its
 * action, so the presence of an author is what separates "we gave you defaults"
 * from "you made this your own".
 */
class GettingStarted
{
    public function __construct(
        private readonly Account $account,
    ) {}

    /**
     * @return Collection<int, array{key: string, route: string, done: bool}>
     */
    public function steps(): Collection
    {
        return new Collection([
            [
                'key' => 'types',
                'route' => route('settings.types.index'),
                'done' => $this->account->catalogTypes()->whereNotNull('created_by_id')->exists(),
            ],
            [
                'key' => 'tags',
                'route' => route('settings.tags.index'),
                'done' => $this->account->tags()->exists(),
            ],
            [
                'key' => 'members',
                'route' => route('settings.members.index'),
                'done' => $this->account->users()->count() > 1 || $this->account->invitations()->whereNull('accepted_at')->exists(),
            ],
            [
                'key' => 'locations',
                'route' => route('locations.index'),
                'done' => $this->account->locations()->whereNotNull('created_by_id')->exists(),
            ],
            [
                'key' => 'collection',
                'route' => route('collections.new'),
                'done' => $this->account->catalogs()->exists(),
            ],
        ]);
    }

    /**
     * How many of the steps are behind the user.
     */
    public function doneCount(): int
    {
        return $this->steps()->where('done', true)->count();
    }
}
