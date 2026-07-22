<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * The condition before and after a piece of work have to be conditions of the
 * copy's own account, the same way the copy form only offers that account's
 * conditions. A condition from another account is not found rather than refused,
 * so a cross tenant id looks exactly like one that does not exist.
 */
trait GuardsMaintenanceConditions
{
    private function guardConditionsBelongToAccount(Account $account, ?int $conditionBeforeId, ?int $conditionAfterId): void
    {
        $ids = array_values(array_filter([$conditionBeforeId, $conditionAfterId], fn (?int $id): bool => $id !== null));

        if ($ids === []) {
            return;
        }

        $owned = $account->itemConditions()->whereIn('id', $ids)->pluck('id')->all();

        foreach ($ids as $id) {
            if (! in_array($id, $owned, true)) {
                throw new ModelNotFoundException('Condition not found');
            }
        }
    }
}
