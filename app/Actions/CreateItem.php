<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CopyStatus;
use App\Enums\FieldTypeEnum;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
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
use App\Models\Series;
use App\Models\Set;
use App\Models\Tag;
use App\Models\User;
use App\Models\Valuation;
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
    use RecordsCopyMoves;

    private Item $item;

    /**
     * @param  list<int>  $tagIds  ids of existing account tags to apply
     * @param  list<string>  $newTagNames  names of new tags to create and apply
     * @param  array<int, string|null>  $customFieldValues  custom field id to raw value
     * @param  list<array{identifier?: string|null, condition_id?: int|null, current_location_id?: int|null, status?: CopyStatus|null, quantity?: int|null, disposed_at?: string|null, note?: string|null, estimated_value?: int|null}>  $copies
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
        private readonly ?Series $series = null,
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

        if ($this->set instanceof Set && $this->set->collection_id !== $this->collection->id) {
            throw new ModelNotFoundException('Set not found');
        }

        // A series is account-wide, so it only has to share the account, not the collection.
        // That is the point of it: one series gathers items from several collections.
        if ($this->series instanceof Series && $this->series->account_id !== $this->collection->account_id) {
            throw new ModelNotFoundException('Series not found');
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
            $locationId = $copy['current_location_id'] ?? null;

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
            'series_id' => $this->series?->id,
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
                'identifier' => $copy['identifier'] ?? null,
                'condition_id' => $copy['condition_id'] ?? null,
                'status' => $copy['status'] ?? CopyStatus::Owned,
                'quantity' => $copy['quantity'] ?? 1,
                'disposed_at' => $copy['disposed_at'] ?? null,
                'note' => $copy['note'] ?? null,
            ]);
            $this->stampAuthorOn($created);

            // The location goes through the move path so creating a copy somewhere
            // opens its first location record rather than only setting the pointer.
            $this->recordCopyMove($created, $copy['current_location_id'] ?? null, $this->user);

            $this->valueCopy($created, $copy['estimated_value'] ?? null);
        }
    }

    /**
     * Record what a copy is reckoned to be worth.
     *
     * The estimated value is no longer a column on the copy, so a figure given
     * with the row opens its valuation history rather than being written to the
     * copy itself.
     */
    private function valueCopy(Copy $copy, ?int $estimatedValue): void
    {
        if ($estimatedValue === null) {
            return;
        }

        $valuation = new Valuation([
            'copy_id' => $copy->id,
            'type' => ValuationType::UserEstimate,
            'amount' => $estimatedValue,
            'currency_code' => $this->collection->currency,
            'valued_at' => now()->toDateString(),
            'confidence' => ValuationConfidence::Unknown,
        ]);

        $this->stampAuthorOn($valuation);
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
