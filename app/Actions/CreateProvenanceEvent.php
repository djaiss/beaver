<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\DatePrecision;
use App\Enums\ItemActionEnum;
use App\Enums\ProvenanceEventType;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Copy;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Record a moment in the story of a copy. Only owners and editors of the copy's
 * account may do so.
 */
class CreateProvenanceEvent
{
    private ProvenanceEvent $event;

    public function __construct(
        private readonly User $user,
        private readonly Copy $copy,
        private readonly ProvenanceEventType $type,
        private readonly string $title,
        private readonly DatePrecision $occurredAtPrecision = DatePrecision::Exact,
        private readonly ?string $occurredAt = null,
        private readonly ?string $description = null,
        private readonly ?string $location = null,
        private readonly ?string $fromParty = null,
        private readonly ?string $toParty = null,
        private readonly ?string $referenceNumber = null,
        private readonly ?string $sourceUrl = null,
        private readonly bool $isVerified = false,
        private readonly ?string $verificationNote = null,
        private readonly ?Transaction $transaction = null,
    ) {}

    public function execute(): ProvenanceEvent
    {
        $this->validate();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->event;
    }

    private function validate(): void
    {
        $account = $this->copy->item->catalog->account;

        if (! $account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if (! $this->transaction instanceof Transaction) {
            return;
        }

        // The transaction has to be one of this copy's own. Linking across
        // copies would attach the money of one object to the story of another.
        if ($this->transaction->copy_id !== $this->copy->id) {
            throw new ModelNotFoundException('Transaction not found');
        }

        // A transaction is one exchange, so it cannot be the source of two
        // separate moments in the story.
        if ($this->transaction->provenanceEvent()->exists()) {
            throw new ModelNotFoundException('Transaction not found');
        }
    }

    private function create(): void
    {
        $this->event = ProvenanceEvent::query()->create([
            'copy_id' => $this->copy->id,
            'transaction_id' => $this->transaction?->id,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            // An undated event keeps no date at all, so nothing is left behind
            // for a later edit to start reading as though it were known.
            'occurred_at' => $this->occurredAtPrecision->carriesDate() ? $this->occurredAt : null,
            'occurred_at_precision' => $this->occurredAtPrecision,
            'location' => $this->location,
            'from_party' => $this->fromParty,
            'to_party' => $this->toParty,
            'reference_number' => $this->referenceNumber,
            'source_url' => $this->sourceUrl,
            'is_verified' => $this->isVerified,
            // A note about how something was verified means nothing when it was
            // not, so it does not survive the flag being off.
            'verification_note' => $this->isVerified ? $this->verificationNote : null,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->event->created_by_id = $this->user->id;
        $this->event->created_by_name = $this->user->getFullName();
        $this->event->updated_by_id = $this->user->id;
        $this->event->updated_by_name = $this->user->getFullName();
        $this->event->save();
    }

    private function log(): void
    {
        $item = $this->copy->item;

        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ProvenanceEventCreation,
            parameters: ['name' => $item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $item,
            user: $this->user,
            action: ItemActionEnum::ProvenanceEventCreation,
            parameters: ['label' => $this->type->label()],
        )->onQueue('low');
    }
}
