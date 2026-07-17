<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update an item. Only owners and editors of its account may do so.
 */
class UpdateItem
{
    public function __construct(
        private readonly User $user,
        private readonly Item $item,
        private string $name,
        private ?string $description = null,
        private readonly ?CollectionType $collectionType = null,
    ) {}

    public function execute(): Item
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->item;
    }

    private function validate(): void
    {
        if (! $this->item->collection->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if (! $this->collectionType instanceof CollectionType) {
            return;
        }

        if (! $this->item->collection->collectionTypes()->whereKey($this->collectionType->id)->exists()) {
            throw new ModelNotFoundException('Type not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
    }

    private function update(): void
    {
        $this->item->name = $this->name;
        $this->item->description = $this->description;
        $this->item->type_id = $this->collectionType?->id;
        $this->item->updated_by_id = $this->user->id;
        $this->item->updated_by_name = $this->user->getFullName();
        $this->item->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ItemUpdate,
            parameters: ['name' => $this->item->name],
        )->onQueue('low');
    }
}
