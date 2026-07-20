<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\DocumentType;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Correct the details of a document: what it is, its name, and the notes and
 * references around it. The file or link itself does not change here, since
 * replacing what is stored is a delete and a fresh upload. Only owners and
 * editors of the document's account may do so.
 */
class UpdateDocument
{
    public function __construct(
        private readonly User $user,
        private readonly Document $document,
        private readonly DocumentType $type,
        private readonly string $name,
        private readonly ?string $description = null,
        private readonly ?string $issuedAt = null,
        private readonly ?string $referenceNumber = null,
    ) {}

    public function execute(): Document
    {
        $this->validate();
        $this->update();
        $this->stampAuthor();
        $this->log();

        return $this->document;
    }

    private function validate(): void
    {
        if (! $this->document->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function update(): void
    {
        $this->document->update([
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'issued_at' => $this->issuedAt,
            'reference_number' => $this->referenceNumber,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->document->updated_by_id = $this->user->id;
        $this->document->updated_by_name = $this->user->getFullName();
        $this->document->save();
    }

    private function log(): void
    {
        $item = $this->document->item();

        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::DocumentUpdate,
            parameters: ['name' => $item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $item,
            user: $this->user,
            action: ItemActionEnum::DocumentUpdate,
            parameters: ['label' => $this->name],
        )->onQueue('low');
    }
}
