<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateItem;
use App\Actions\DestroyItem;
use App\Actions\UpdateItem;
use App\Enums\CopyStatus;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Category;
use App\Traits\SuggestsTags;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller
{
    use SuggestsTags;

    public function show(Request $request): View
    {
        $item = $request->attributes->get('item');
        $item->load([
            'photos',
            'copies',
            'tags',
            'category.parent',
            'set',
            'series',
            'catalogType.customFieldGroups' => fn ($query) => $query->orderBy('position')->orderBy('id'),
            'catalogType.customFieldGroups.customFields' => fn ($query) => $query->orderBy('position')->orderBy('id'),
            'catalogType.ungroupedCustomFields' => fn ($query) => $query->orderBy('position')->orderBy('id'),
            'customFieldValues',
        ]);

        return view('app.items.show', [
            'tags' => $this->accountTags($request),
            // The set counts what is owned. How many entries a set should hold
            // is not tracked yet, so completion cannot be worked out.
            'setItemCount' => $item->set?->items()->count() ?? 0,
            // The series card reports its reach, which is the whole point of a series.
            'seriesItemCount' => $item->series?->items()->count() ?? 0,
            'seriesCatalogCount' => $item->series?->items()->distinct()->count('catalog_id') ?? 0,
        ]);
    }

    public function new(Request $request): View
    {
        $account = $request->user()->account;
        $catalog = $request->attributes->get('catalog');

        return view('app.items.new', [
            'types' => $catalog->catalogTypes()->with('customFields')->orderBy('name')->get(),
            'categories' => $this->categoryOptions($catalog),
            'sets' => $catalog->sets()->get()->sortBy('name')->values(),
            'series' => $account->series()->get()->sortBy(fn ($one): string => mb_strtolower($one->name))->values(),
            'conditions' => $account->itemConditions()->orderBy('name')->get(),
            'locations' => $account->locations()->orderBy('name')->get(),
            'tags' => $this->accountTags($request),
        ]);
    }

    public function edit(Request $request): View
    {
        $account = $request->user()->account;
        $catalog = $request->attributes->get('catalog');

        $request->attributes->get('item')->load(['tags', 'copies', 'customFieldValues', 'photos', 'mainPhoto']);

        return view('app.items.edit', [
            'types' => $catalog->catalogTypes()->with('customFields')->orderBy('name')->get(),
            'categories' => $this->categoryOptions($catalog),
            'sets' => $catalog->sets()->get()->sortBy('name')->values(),
            'series' => $account->series()->get()->sortBy(fn ($one): string => mb_strtolower($one->name))->values(),
            'conditions' => $account->itemConditions()->orderBy('name')->get(),
            'locations' => $account->locations()->orderBy('name')->get(),
            'tags' => $this->accountTags($request),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $account = $request->user()->account;
        $catalog = $request->attributes->get('catalog');
        $item = $request->attributes->get('item');

        $validated = $this->validateItem($request);

        new UpdateItem(
            user: $request->user(),
            item: $item,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            catalogType: isset($validated['type_id'])
                ? $account->catalogTypes()->find($validated['type_id'])
                : null,
            category: isset($validated['category_id'])
                ? $catalog->categories()->find($validated['category_id'])
                : null,
            set: isset($validated['set_id'])
                ? $catalog->sets()->find($validated['set_id'])
                : null,
            series: isset($validated['series_id'])
                ? $account->series()->find($validated['series_id'])
                : null,
            tagIds: $validated['tag_ids'] ?? [],
            newTagNames: $validated['new_tags'] ?? [],
            customFieldValues: $validated['custom_fields'] ?? [],
            copies: $this->copies($validated['copies'] ?? []),
            photos: $this->photos($request),
            deletedPhotoIds: array_map(intval(...), $validated['deleted_photos'] ?? []),
            mainPhotoId: isset($validated['main_photo_id']) ? (int) $validated['main_photo_id'] : null,
        )->execute();

        return to_route('items.show', [$catalog->id, $item->id])
            ->with('status', __('Item updated'))
            ->with('status_description', __('Your changes to the item were saved.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        new DestroyItem(
            user: $request->user(),
            item: $request->attributes->get('item'),
        )->execute();

        return to_route('collections.show', $request->attributes->get('catalog')->id)
            ->with('status', __('Item deleted'))
            ->with('status_description', __('The item was removed from the collection.'));
    }

    public function create(Request $request): RedirectResponse
    {
        $account = $request->user()->account;
        $catalog = $request->attributes->get('catalog');

        $validated = $this->validateItem($request);

        $type = isset($validated['type_id'])
            ? $account->catalogTypes()->find($validated['type_id'])
            : null;

        $category = isset($validated['category_id'])
            ? $catalog->categories()->find($validated['category_id'])
            : null;

        $set = isset($validated['set_id'])
            ? $catalog->sets()->find($validated['set_id'])
            : null;

        $series = isset($validated['series_id'])
            ? $account->series()->find($validated['series_id'])
            : null;

        new CreateItem(
            user: $request->user(),
            catalog: $catalog,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            catalogType: $type,
            category: $category,
            set: $set,
            series: $series,
            tagIds: $validated['tag_ids'] ?? [],
            newTagNames: $validated['new_tags'] ?? [],
            customFieldValues: $validated['custom_fields'] ?? [],
            copies: $this->copies($validated['copies'] ?? []),
            photos: $this->photos($request),
        )->execute();

        return to_route('collections.show', $catalog->id)
            ->with('status', __('Item added'))
            ->with('status_description', __('Your new item is now in the collection.'));
    }

    /**
     * The add and the edit screens submit the same shape, so they share their rules.
     * A copy carrying an id edits that copy, and one without it adds a copy.
     *
     * @return array<string, mixed>
     */
    private function validateItem(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'set_id' => ['nullable', 'integer'],
            'series_id' => ['nullable', 'integer'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer'],
            'new_tags' => ['array'],
            'new_tags.*' => ['string', 'max:255'],
            'custom_fields' => ['array'],
            'copies' => ['array'],
            'copies.*.id' => ['nullable', 'integer'],
            'copies.*.identifier' => ['nullable', 'string', 'max:255'],
            'copies.*.item_condition_id' => ['nullable', 'integer'],
            'copies.*.current_location_id' => ['nullable', 'integer'],
            'copies.*.status' => ['nullable', Rule::enum(CopyStatus::class)],
            'copies.*.quantity' => ['nullable', 'integer', 'min:1'],
            'copies.*.disposed_at' => ['nullable', 'date'],
            'copies.*.note' => ['nullable', 'string', 'max:2000'],
            'copies.*.estimated_value' => ['nullable', 'numeric', 'min:0'],
            'photos' => ['array'],
            'photos.*' => ['image', 'max:10240'],
            'deleted_photos' => ['array'],
            'deleted_photos.*' => ['integer'],
            'main_photo_id' => ['nullable', 'integer'],
        ]);
    }

    /**
     * Turn the validated copy rows into the shape the action expects, converting
     * the estimated value from currency units into integer cents.
     *
     * The location is only carried when the row actually submitted it. The edit
     * form drops the field for an existing copy, whose location is changed through
     * the move action instead, so an absent key leaves the copy where it is rather
     * than reading as a move to nowhere.
     *
     * @param  array<int, array<string, mixed>>  $copies
     * @return list<array{id: int|null, identifier: string|null, item_condition_id: int|null, current_location_id?: int|null, status: CopyStatus, quantity: int, disposed_at: string|null, note: string|null, estimated_value: int|null}>
     */
    private function copies(array $copies): array
    {
        return collect($copies)
            ->map(function (array $copy): array {
                $shaped = [
                    'id' => $this->toId($copy['id'] ?? null),
                    'identifier' => $this->toText($copy['identifier'] ?? null),
                    'item_condition_id' => $this->toId($copy['item_condition_id'] ?? null),
                    'status' => CopyStatus::tryFrom((string) ($copy['status'] ?? '')) ?? CopyStatus::Owned,
                    'quantity' => max(1, (int) ($copy['quantity'] ?? 1)),
                    'disposed_at' => $this->toText($copy['disposed_at'] ?? null),
                    'note' => $this->toText($copy['note'] ?? null),
                    'estimated_value' => $this->toCents($copy['estimated_value'] ?? null),
                ];

                if (array_key_exists('current_location_id', $copy)) {
                    $shaped['current_location_id'] = $this->toId($copy['current_location_id']);
                }

                return $shaped;
            })
            ->all();
    }

    /**
     * The uploaded photos, in the order the browser sent them. An input that
     * was left alone sends nothing at all.
     *
     * @return list<UploadedFile>
     */
    private function photos(Request $request): array
    {
        return array_values(array_filter(
            $request->file('photos', []),
            fn (mixed $photo): bool => $photo instanceof UploadedFile,
        ));
    }

    /**
     * The categories of a collection, flattened for a <select> with each child listed
     * directly under its parent and carrying its depth so the option can be indented.
     *
     * Names are encrypted, so the database cannot sort them and the ordering is done
     * in memory instead.
     *
     * @return list<array{id: int, name: string, depth: int}>
     */
    private function categoryOptions(Catalog $catalog): array
    {
        return $this->flattenCategories($catalog->categories()->get());
    }

    /**
     * @param  EloquentCollection<int, Category>  $categories
     * @return list<array{id: int, name: string, depth: int}>
     */
    private function flattenCategories(EloquentCollection $categories, ?int $parentId = null, int $depth = 0): array
    {
        return $categories
            ->where('parent_id', $parentId)
            ->sortBy('name')
            ->flatMap(fn (Category $category): array => [
                ['id' => $category->id, 'name' => $category->name, 'depth' => $depth],
                ...$this->flattenCategories($categories, $category->id, $depth + 1),
            ])
            ->values()
            ->all();
    }

    /**
     * A form sends an id as a string, and the actions match it against the ids
     * they own with a strict comparison, so it has to arrive as an integer.
     */
    private function toId(mixed $id): ?int
    {
        if ($id === null || $id === '') {
            return null;
        }

        return (int) $id;
    }

    /**
     * An input the user left alone still submits, so its empty string is brought
     * back to null rather than stored as a blank value.
     */
    private function toText(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function toCents(mixed $amount): ?int
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        return (int) round((float) $amount * 100);
    }
}
