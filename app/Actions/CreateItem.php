<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Create an item within a collection. Only owners and editors of the
 * collection's account may do so.
 */
class CreateItem
{
    private Item $item;

    public function __construct(
        private readonly User $user,
        private readonly Collection $collection,
        private string $name,
        private ?string $description = null,
        private readonly ?CollectionType $collectionType = null,
    ) {}

    public function execute(): Item
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->item;
    }

    private function validate(): void
    {
        if (! $this->collection->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if (! $this->collectionType instanceof CollectionType) {
            return;
        }

        if (! $this->collection->collectionTypes()->whereKey($this->collectionType->id)->exists()) {
            throw new ModelNotFoundException('Type not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
    }

    private function create(): void
    {
        $this->item = Item::query()->create([
            'collection_id' => $this->collection->id,
            'type_id' => $this->collectionType?->id,
            'name' => $this->name,
            'description' => $this->description,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->item->created_by_id = $this->user->id;
        $this->item->created_by_name = $this->user->getFullName();
        $this->item->updated_by_id = $this->user->id;
        $this->item->updated_by_name = $this->user->getFullName();
        $this->item->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ItemCreation,
            parameters: ['name' => $this->item->name],
        )->onQueue('low');
    }
}
