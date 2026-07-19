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
use App\Models\CollectionType;
use App\Models\Copy;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Models\Set;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Update an item, together with its tags, custom field values and copies. Only
 * owners and editors of its account may do so.
 *
 * A model that is not given is cleared, but an array that is not given is left
 * alone. The difference matters because the API only ever edits the catalog
 * fields, and renaming an item there must not wipe its tags or its copies.
 */
class UpdateItem
{
    /**
     * The values that moved, captured before the item is written so the
     * activity tab can show what they moved from.
     *
     * @var list<array{label: string, from?: string|null, to?: string|null}>
     */
    private array $changes = [];

    /**
     * @param  list<int>|null  $tagIds  ids of existing account tags to apply
     * @param  list<string>|null  $newTagNames  names of new tags to create and apply
     * @param  array<int, string|null>|null  $customFieldValues  custom field id to raw value
     * @param  list<array{id?: int|null, condition_id?: int|null, location_id?: int|null, acquired_at?: string|null, price_paid?: int|null, estimated_value?: int|null}>|null  $copies
     * @param  list<UploadedFile>  $photos  new photos, appended after the ones the item already has
     * @param  list<int>  $deletedPhotoIds  ids of photos of this item to remove
     * @param  int|null  $mainPhotoId  id of the photo to make the cover, among those the item keeps
     */
    public function __construct(
        private readonly User $user,
        private readonly Item $item,
        private string $name,
        private ?string $description = null,
        private readonly ?CollectionType $collectionType = null,
        private readonly ?Category $category = null,
        private readonly ?Set $set = null,
        private readonly ?array $tagIds = null,
        private readonly ?array $newTagNames = null,
        private readonly ?array $customFieldValues = null,
        private readonly ?array $copies = null,
        private readonly array $photos = [],
        private readonly array $deletedPhotoIds = [],
        private readonly ?int $mainPhotoId = null,
    ) {}

    public function execute(): Item
    {
        $this->validate();
        $this->sanitize();
        $this->captureChanges();

        DB::transaction(function (): void {
            $this->update();
            $this->syncTags();
            $this->syncCustomFieldValues();
            $this->syncCopies();
        });

        $this->syncPhotos();
        $this->log();

        return $this->item;
    }

    private function validate(): void
    {
        $collection = $this->item->collection;

        if (! $collection->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if ($this->collectionType instanceof CollectionType && ! $collection->collectionTypes()->whereKey($this->collectionType->id)->exists()) {
            throw new ModelNotFoundException('Type not found');
        }

        if ($this->category instanceof Category && $this->category->collection_id !== $collection->id) {
            throw new ModelNotFoundException('Category not found');
        }

        if ($this->set instanceof Set && $this->set->collection_id !== $collection->id) {
            throw new ModelNotFoundException('Set not found');
        }

        $this->validateTags();
        $this->validateCopies();
    }

    private function validateTags(): void
    {
        if ($this->tagIds === null || $this->tagIds === []) {
            return;
        }

        $ownedCount = $this->item->collection->account->tags()->whereKey($this->tagIds)->count();

        if ($ownedCount !== count(array_unique($this->tagIds))) {
            throw new ModelNotFoundException('Tag not found');
        }
    }

    private function validateCopies(): void
    {
        if ($this->copies === null) {
            return;
        }

        $account = $this->item->collection->account;
        $conditionIds = $account->conditions()->pluck('id')->all();
        $locationIds = $account->locations()->pluck('id')->all();
        $copyIds = $this->item->copies()->pluck('id')->all();

        foreach ($this->copies as $copy) {
            $id = $copy['id'] ?? null;
            $conditionId = $copy['condition_id'] ?? null;
            $locationId = $copy['location_id'] ?? null;

            if ($id !== null && ! in_array($id, $copyIds, true)) {
                throw new ModelNotFoundException('Copy not found');
            }

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

    /**
     * Read what is about to move, while the item still holds its old values.
     */
    private function captureChanges(): void
    {
        $this->changes = array_values(array_filter([
            $this->change('Name', $this->item->name, $this->name),
            $this->describedChange(),
            $this->change('Type', $this->item->collectionType?->name, $this->collectionType?->name),
            $this->change('Category', $this->item->category?->name, $this->category?->name),
            $this->change('Set', $this->item->set?->name, $this->set?->name),
        ]));
    }

    /**
     * @return array{label: string, from: string|null, to: string|null}|null
     */
    private function change(string $label, ?string $from, ?string $to): ?array
    {
        if ($from === $to) {
            return null;
        }

        return ['label' => $label, 'from' => $from, 'to' => $to];
    }

    /**
     * A description is far too long to sit in a chip, so the activity tab only
     * reports that it moved.
     *
     * @return array{label: string}|null
     */
    private function describedChange(): ?array
    {
        if ($this->item->description === $this->description) {
            return null;
        }

        return ['label' => 'Description'];
    }

    private function update(): void
    {
        $this->item->name = $this->name;
        $this->item->description = $this->description;
        $this->item->type_id = $this->collectionType?->id;
        $this->item->category_id = $this->category?->id;
        $this->item->set_id = $this->set?->id;
        $this->item->updated_by_id = $this->user->id;
        $this->item->updated_by_name = $this->user->getFullName();
        $this->item->save();
    }

    private function syncTags(): void
    {
        if ($this->tagIds === null) {
            return;
        }

        $tagIds = $this->tagIds;

        foreach ($this->newTagNames ?? [] as $name) {
            $name = TextSanitizer::plainText($name);

            if ($name === '') {
                continue;
            }

            $tag = Tag::query()->create([
                'account_id' => $this->item->collection->account_id,
                'name' => $name,
            ]);
            $this->stampAuthorOn($tag);

            $tagIds[] = $tag->id;
        }

        $this->item->tags()->sync($tagIds);
    }

    /**
     * Values are written for the fields of the type the item now carries, and
     * anything left over from a previous type is dropped: the item screen has
     * no way to show a value whose field no longer applies.
     */
    private function syncCustomFieldValues(): void
    {
        if ($this->customFieldValues === null) {
            return;
        }

        $fields = $this->collectionType?->customFields()->get()->keyBy('id') ?? collect();
        $existing = $this->item->customFieldValues()->get()->keyBy('custom_field_id');

        foreach ($this->customFieldValues as $fieldId => $value) {
            $field = $fields->get($fieldId);

            if (! $field instanceof CustomField) {
                continue;
            }

            $value = $this->cleanValue($field, $value);

            if ($value === null) {
                $existing->get($fieldId)?->delete();

                continue;
            }

            CustomFieldValue::query()->updateOrCreate(
                ['item_id' => $this->item->id, 'custom_field_id' => $fieldId],
                ['value' => $value],
            );
        }

        $this->item->customFieldValues()
            ->whereNotIn('custom_field_id', $fields->keys()->all())
            ->delete();
    }

    private function cleanValue(CustomField $field, string|int|null $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($field->field_type === FieldTypeEnum::Rating) {
            return $this->rating($value);
        }

        return TextSanitizer::plainText((string) $value);
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

    /**
     * A row carrying an id updates the copy it names, a row without one adds a
     * copy, and a copy the form no longer lists is deleted.
     */
    private function syncCopies(): void
    {
        if ($this->copies === null) {
            return;
        }

        $keptIds = [];

        foreach ($this->copies as $copy) {
            $attributes = [
                'condition_id' => $copy['condition_id'] ?? null,
                'location_id' => $copy['location_id'] ?? null,
                'acquired_at' => $copy['acquired_at'] ?? null,
                'price_paid' => $copy['price_paid'] ?? null,
                'estimated_value' => $copy['estimated_value'] ?? null,
            ];

            $id = $copy['id'] ?? null;

            if ($id !== null) {
                $existing = $this->item->copies()->findOrFail($id);
                $existing->fill($attributes);
                $this->stampAuthorOn($existing);

                $keptIds[] = $id;

                continue;
            }

            $created = Copy::query()->create(['item_id' => $this->item->id, ...$attributes]);
            $created->created_by_id = $this->user->id;
            $created->created_by_name = $this->user->getFullName();
            $this->stampAuthorOn($created);

            $keptIds[] = $created->id;
        }

        // A soft delete only writes deleted_at, so who did it is stamped first.
        $this->item->copies()->whereNotIn('id', $keptIds)->get()->each(function (Copy $copy): void {
            $copy->deleted_by_id = $this->user->id;
            $copy->deleted_by_name = $this->user->getFullName();
            $copy->saveQuietly();
            $copy->delete();
        });
    }

    /**
     * Photos are removed first, so the cover can be handed to a photo that
     * survives, and so an item emptied of its photos lets the first new one
     * take the role on its own.
     */
    private function syncPhotos(): void
    {
        $this->deletePhotos();
        $this->addPhotos();
        $this->setMainPhoto();
    }

    private function deletePhotos(): void
    {
        foreach ($this->deletedPhotoIds as $photoId) {
            $photo = $this->item->photos()->find($photoId);

            if (! $photo instanceof ItemPhoto) {
                continue;
            }

            new DestroyItemPhoto(
                user: $this->user,
                itemPhoto: $photo,
            )->execute();
        }
    }

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

    /**
     * A photo that was removed in the same request, or that belongs to another
     * item, cannot become the cover. Deleting the current cover already
     * promotes another one, so leaving the choice out is not an error.
     */
    private function setMainPhoto(): void
    {
        if ($this->mainPhotoId === null) {
            return;
        }

        $photo = $this->item->photos()->find($this->mainPhotoId);

        if (! $photo instanceof ItemPhoto || $photo->is_main) {
            return;
        }

        new SetMainItemPhoto(
            user: $this->user,
            itemPhoto: $photo,
        )->execute();
    }

    private function stampAuthorOn(Model $model): void
    {
        $model->setAttribute('updated_by_id', $this->user->id);
        $model->setAttribute('updated_by_name', $this->user->getFullName());
        $model->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ItemUpdate,
            parameters: ['name' => $this->item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $this->item,
            user: $this->user,
            action: ItemActionEnum::ItemUpdate,
            parameters: $this->changes === [] ? null : ['changes' => $this->changes],
        )->onQueue('low');
    }
}
