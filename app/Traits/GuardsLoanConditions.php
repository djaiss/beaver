<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * The condition on the way out and on the way back have to be conditions of the
 * copy's own account, the same way the copy form only offers that account's
 * conditions. A condition from another account is not found rather than refused,
 * so a cross tenant id looks exactly like one that does not exist.
 */
trait GuardsLoanConditions
{
    private function guardConditionsBelongToAccount(Account $account, ?int $conditionOutId, ?int $conditionInId): void
    {
        $ids = array_values(array_filter([$conditionOutId, $conditionInId], fn (?int $id): bool => $id !== null));

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
