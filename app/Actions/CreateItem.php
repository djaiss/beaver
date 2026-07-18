<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\FieldTypeEnum;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Copy;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Item;
use App\Models\Set;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Create an item within a collection, together with its copies, tags and
 * custom field values. Only owners and editors of the collection's account may
 * do so.
 */
class CreateItem
{
    private Item $item;

    /**
     * @param  list<int>  $tagIds  ids of existing account tags to apply
     * @param  list<string>  $newTagNames  names of new tags to create and apply
     * @param  array<int, string|null>  $customFieldValues  custom field id to raw value
     * @param  list<array{condition_id?: int|null, location_id?: int|null, acquired_at?: string|null, price_paid?: int|null, estimated_value?: int|null}>  $copies
     * @param  list<UploadedFile>  $photos  in the order they should appear, the first becoming the cover
     */
    public function __construct(
        private readonly User $user,
        private readonly Collection $collection,
        private string $name,
        private ?string $description = null,
        private readonly ?CollectionType $collectionType = null,
        private readonly ?Category $category = null,
        private readonly ?Set $set = null,
        private readonly array $tagIds = [],
        private readonly array $newTagNames = [],
        private readonly array $customFieldValues = [],
        private readonly array $copies = [],
        private readonly array $photos = [],
    ) {}

    public function execute(): Item
    {
        $this->validate();
        $this->sanitize();

        DB::transaction(function (): void {
            $this->create();
            $this->stampAuthor();
            $this->syncTags();
            $this->createCustomFieldValues();
            $this->createCopies();
        });

        $this->addPhotos();
        $this->log();

        return $this->item;
    }

    private function validate(): void
    {
        if (! $this->collection->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if ($this->collectionType instanceof CollectionType && ! $this->collection->collectionTypes()->whereKey($this->collectionType->id)->exists()) {
            throw new ModelNotFoundException('Type not found');
        }

        if ($this->category instanceof Category && $this->category->collection_id !== $this->collection->id) {
            throw new ModelNotFoundException('Category not found');
        }

        if ($this->set instanceof Set && $this->set->account_id !== $this->collection->account_id) {
            throw new ModelNotFoundException('Set not found');
        }

        $this->validateTags();
        $this->validateCopies();
    }

    private function validateTags(): void
    {
        if ($this->tagIds === []) {
            return;
        }

        $ownedCount = $this->collection->account->tags()->whereKey($this->tagIds)->count();

        if ($ownedCount !== count(array_unique($this->tagIds))) {
            throw new ModelNotFoundException('Tag not found');
        }
    }

    private function validateCopies(): void
    {
        $conditionIds = $this->collection->account->conditions()->pluck('id')->all();
        $locationIds = $this->collection->account->locations()->pluck('id')->all();

        foreach ($this->copies as $copy) {
            $conditionId = $copy['condition_id'] ?? null;
            $locationId = $copy['location_id'] ?? null;

            if ($conditionId !== null && ! in_array($conditionId, $conditionIds, true)) {
                throw new ModelNotFoundException('Condition not found');
            }

            if ($locationId !== null && ! in_array($locationId, $locationIds, true)) {
                throw new ModelNotFoundException('Location not found');
            }
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
            'category_id' => $this->category?->id,
            'type_id' => $this->collectionType?->id,
            'set_id' => $this->set?->id,
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

    private function syncTags(): void
    {
        $tagIds = $this->tagIds;

        foreach ($this->newTagNames as $name) {
            $name = TextSanitizer::plainText($name);

            if ($name === '') {
                continue;
            }

            $tag = Tag::query()->create([
                'account_id' => $this->collection->account_id,
                'name' => $name,
            ]);
            $this->stampAuthorOn($tag);

            $tagIds[] = $tag->id;
        }

        $this->item->tags()->sync($tagIds);
    }

    private function createCustomFieldValues(): void
    {
        if (! $this->collectionType instanceof CollectionType) {
            return;
        }

        $fields = $this->collectionType->customFields()->get()->keyBy('id');

        foreach ($this->customFieldValues as $fieldId => $value) {
            $field = $fields->get($fieldId);

            if (! $field instanceof CustomField) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $value = $field->field_type === FieldTypeEnum::Rating
                ? $this->rating($value)
                : TextSanitizer::plainText((string) $value);

            if ($value === null) {
                continue;
            }

            CustomFieldValue::query()->create([
                'item_id' => $this->item->id,
                'custom_field_id' => $fieldId,
                'value' => $value,
            ]);
        }
    }

    /**
     * A rating is a whole number of stars, so anything outside the scale is dropped
     * rather than stored as junk.
     */
    private function rating(string|int $value): ?string
    {
        $stars = filter_var($value, FILTER_VALIDATE_INT);

        if ($stars === false || $stars < 1 || $stars > FieldTypeEnum::MAX_RATING) {
            return null;
        }

        return (string) $stars;
    }

    private function createCopies(): void
    {
        foreach ($this->copies as $copy) {
            $created = Copy::query()->create([
                'item_id' => $this->item->id,
                'condition_id' => $copy['condition_id'] ?? null,
                'location_id' => $copy['location_id'] ?? null,
                'acquired_at' => $copy['acquired_at'] ?? null,
                'price_paid' => $copy['price_paid'] ?? null,
                'estimated_value' => $copy['estimated_value'] ?? null,
            ]);
            $this->stampAuthorOn($created);
        }
    }

    /**
     * The photos are added in the order they were given. An item without any
     * photo yet promotes the first one to cover on its own.
     */
    private function addPhotos(): void
    {
        foreach ($this->photos as $photo) {
            if (! $photo instanceof UploadedFile) {
                continue;
            }

            new AddItemPhoto(
                user: $this->user,
                item: $this->item,
                file: $photo,
            )->execute();
        }
    }

    private function stampAuthorOn(Model $model): void
    {
        $model->setAttribute('created_by_id', $this->user->id);
        $model->setAttribute('created_by_name', $this->user->getFullName());
        $model->setAttribute('updated_by_id', $this->user->id);
        $model->setAttribute('updated_by_name', $this->user->getFullName());
        $model->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ItemCreation,
            parameters: ['name' => $this->item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $this->item,
            user: $this->user,
            action: ItemActionEnum::ItemCreation,
        )->onQueue('low');
    }
}
