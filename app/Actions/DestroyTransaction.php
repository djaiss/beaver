<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a transaction. Only owners and editors of its account may do so.
 */
class DestroyTransaction
{
    public function __construct(
        private readonly User $user,
        private readonly Transaction $transaction,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->unlinkProvenance();
        $this->log();
        $this->transaction->delete();
    }

    private function validate(): void
    {
        $account = $this->transaction->copy->item->catalog->account;

        if (! $account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    /**
     * Release the provenance event this transaction produced, if any.
     *
     * The money was a fact about the exchange. The moment in the object's story
     * outlives the record of what was paid for it, so the event stays and simply
     * loses its link.
     *
     * The foreign key says the same thing, but it is only a backstop: it is not
     * enforced on every connection the application runs on, so the rule lives
     * here where it can be relied on and tested.
     */
    private function unlinkProvenance(): void
    {
        $this->transaction->provenanceEvent()->update(['transaction_id' => null]);
    }

    /**
     * Log before the row goes, so the entry can still describe what was deleted.
     */
    private function log(): void
    {
        $item = $this->transaction->copy->item;

        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TransactionDeletion,
            parameters: ['name' => $item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $item,
            user: $this->user,
            action: ItemActionEnum::TransactionDeletion,
            parameters: ['label' => $this->transaction->type->label()],
        )->onQueue('low');
    }
}
