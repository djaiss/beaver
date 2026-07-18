<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\VisibilityEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Update a collection. Only owners and editors of its account may do so.
 */
class UpdateCollection
{
    /**
     * @param  array<string, mixed>|null  $settings
     * @param  array<int, int>|null  $collectionTypeIds  Null leaves the linked types alone.
     */
    public function __construct(
        private readonly User $user,
        private readonly Collection $collection,
        private string $name,
        private ?string $description = null,
        private ?string $emoji = null,
        private string $visibility = VisibilityEnum::Private->value,
        private ?string $currency = null,
        private ?array $settings = null,
        private ?array $collectionTypeIds = null,
    ) {}

    public function execute(): Collection
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->syncCollectionTypes();
        $this->log();

        return $this->collection;
    }

    private function validate(): void
    {
        if (! $this->userCanManageCollections()) {
            throw new ModelNotFoundException('Account not found');
        }

        if (VisibilityEnum::tryFrom($this->visibility) === null) {
            throw ValidationException::withMessages(['visibility' => 'Invalid visibility']);
        }
    }

    private function userCanManageCollections(): bool
    {
        return in_array(
            $this->collection->account->roleFor($this->user),
            [PermissionEnum::Owner->value, PermissionEnum::Editor->value],
            true,
        );
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
        $this->emoji = TextSanitizer::nullablePlainText($this->emoji);
    }

    private function update(): void
    {
        $this->collection->name = $this->name;
        $this->collection->description = $this->description;
        $this->collection->emoji = $this->emoji;
        $this->collection->visibility = VisibilityEnum::from($this->visibility);
        $this->collection->currency = $this->currency;
        $this->collection->settings = $this->settings;
        $this->collection->updated_by_id = $this->user->id;
        $this->collection->updated_by_name = $this->user->getFullName();
        $this->collection->save();
    }

    /**
     * Callers that do not manage types, such as the API, pass null and leave
     * the existing links untouched.
     */
    private function syncCollectionTypes(): void
    {
        if ($this->collectionTypeIds === null) {
            return;
        }

        $ids = $this->collection->account->collectionTypes()
            ->whereIn('id', $this->collectionTypeIds)
            ->pluck('id')
            ->all();

        $this->collection->collectionTypes()->sync($ids);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CollectionUpdate,
            parameters: ['name' => $this->collection->name],
        )->onQueue('low');
    }
}
