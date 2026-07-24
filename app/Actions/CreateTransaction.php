<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ItemActionEnum;
use App\Enums\TransactionType;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Copy;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Record a transaction against a copy. Only owners and editors of the copy's
 * account may do so.
 */
class CreateTransaction
{
    private Transaction $transaction;

    public function __construct(
        private readonly User $user,
        private readonly Copy $copy,
        private readonly TransactionType $type,
        private readonly string $occurredAt,
        private readonly ?string $counterparty = null,
        private readonly ?int $amount = null,
        private readonly ?string $currencyCode = null,
        private readonly ?int $taxAmount = null,
        private readonly ?int $feeAmount = null,
        private readonly ?int $shippingAmount = null,
        private readonly ?int $totalAmount = null,
        private readonly ?string $referenceNumber = null,
        private readonly ?string $sourceUrl = null,
        private readonly ?string $note = null,
    ) {}

    public function execute(): Transaction
    {
        $this->validate();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->transaction;
    }

    private function validate(): void
    {
        $account = $this->copy->item->catalog->account;

        if (! $account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function create(): void
    {
        $this->transaction = Transaction::query()->create([
            'copy_id' => $this->copy->id,
            'type' => $this->type,
            'counterparty' => $this->counterparty,
            'amount' => $this->amount,
            // Every amount on the row is in one currency, and the collection's
            // is the sensible default when the caller does not say.
            'currency_code' => $this->currencyCode ?? $this->copy->item->catalog->currency,
            'tax_amount' => $this->taxAmount,
            'fee_amount' => $this->feeAmount,
            'shipping_amount' => $this->shippingAmount,
            'total_amount' => $this->totalAmount,
            'occurred_at' => $this->occurredAt,
            'reference_number' => $this->referenceNumber,
            'source_url' => $this->sourceUrl,
            'note' => $this->note,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->transaction->created_by_id = $this->user->id;
        $this->transaction->created_by_name = $this->user->getFullName();
        $this->transaction->updated_by_id = $this->user->id;
        $this->transaction->updated_by_name = $this->user->getFullName();
        $this->transaction->save();
    }

    private function log(): void
    {
        $item = $this->copy->item;

        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TransactionCreation,
            parameters: ['name' => $item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $item,
            user: $this->user,
            action: ItemActionEnum::TransactionCreation,
            parameters: ['label' => $this->type->label()],
        )->onQueue('low');
    }
}
