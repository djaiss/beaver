<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateItem;
use App\Actions\UpdateItem;
use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    use FindsItems;

    public function show(Request $request, int $collection, int $item): View
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, [
            'photos',
            'copies',
            'tags',
            'category',
            'set',
            'collectionType.customFieldGroups' => fn ($query) => $query->orderBy('position')->orderBy('id'),
            'collectionType.customFieldGroups.customFields' => fn ($query) => $query->orderBy('position')->orderBy('id'),
            'collectionType.ungroupedCustomFields' => fn ($query) => $query->orderBy('position')->orderBy('id'),
            'customFieldValues',
        ]);

        return view('app.items.show', [
            'collection' => $collectionModel,
            'item' => $itemModel,
            'tags' => $this->accountTags($request),
            // The set counts what is owned. How many entries a set should hold
            // is not tracked yet, so completion cannot be worked out.
            'setItemCount' => $itemModel->set?->items()->count() ?? 0,
        ]);
    }

    public function new(Request $request, int $collection): View
    {
        $account = $request->user()->account;

        $collectionModel = $this->findCollection($request, $collection);

        return view('app.items.new', [
            'collection' => $collectionModel,
            'types' => $collectionModel->collectionTypes()->with('customFields')->orderBy('name')->get(),
            'categories' => $collectionModel->categories()->orderBy('name')->get(),
            'sets' => $account->sets()->orderBy('name')->get(),
            'conditions' => $account->conditions()->orderBy('name')->get(),
            'locations' => $account->locations()->orderBy('name')->get(),
            'tags' => $this->accountTags($request),
        ]);
    }

    public function edit(Request $request, int $collection, int $item): View
    {
        $account = $request->user()->account;

        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item);

        return view('app.items.edit', [
            'collection' => $collectionModel,
            'item' => $itemModel,
            'types' => $collectionModel->collectionTypes()->with('customFields')->orderBy('name')->get(),
            'categories' => $collectionModel->categories()->orderBy('name')->get(),
            'sets' => $account->sets()->orderBy('name')->get(),
            'conditions' => $account->conditions()->orderBy('name')->get(),
            'locations' => $account->locations()->orderBy('name')->get(),
            'tags' => $this->accountTags($request),
        ]);
    }

    public function update(Request $request, int $collection, int $item): RedirectResponse
    {
        $account = $request->user()->account;

        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item);

        $validated = $this->validateItem($request);

        new UpdateItem(
            user: $request->user(),
            item: $itemModel,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            collectionType: isset($validated['type_id'])
                ? $account->collectionTypes()->find($validated['type_id'])
                : null,
            category: isset($validated['category_id'])
                ? $collectionModel->categories()->find($validated['category_id'])
                : null,
            set: isset($validated['set_id'])
                ? $account->sets()->find($validated['set_id'])
                : null,
            tagIds: $validated['tag_ids'] ?? [],
            newTagNames: $validated['new_tags'] ?? [],
            customFieldValues: $validated['custom_fields'] ?? [],
            copies: $this->copies($validated['copies'] ?? []),
            coverPhoto: $request->file('cover'),
        )->execute();

        return to_route('items.show', [$collectionModel->id, $itemModel->id])
            ->with('status', __('Item updated'))
            ->with('status_description', __('Your changes to the item were saved.'));
    }

    public function create(Request $request, int $collection): RedirectResponse
    {
        $account = $request->user()->account;

        $collectionModel = $this->findCollection($request, $collection);

        $validated = $this->validateItem($request);

        $type = isset($validated['type_id'])
            ? $account->collectionTypes()->find($validated['type_id'])
            : null;

        $category = isset($validated['category_id'])
            ? $collectionModel->categories()->find($validated['category_id'])
            : null;

        $set = isset($validated['set_id'])
            ? $account->sets()->find($validated['set_id'])
            : null;

        new CreateItem(
            user: $request->user(),
            collection: $collectionModel,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            collectionType: $type,
            category: $category,
            set: $set,
            tagIds: $validated['tag_ids'] ?? [],
            newTagNames: $validated['new_tags'] ?? [],
            customFieldValues: $validated['custom_fields'] ?? [],
            copies: $this->copies($validated['copies'] ?? []),
            coverPhoto: $request->file('cover'),
        )->execute();

        return to_route('collections.show', $collectionModel->id)
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
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer'],
            'new_tags' => ['array'],
            'new_tags.*' => ['string', 'max:255'],
            'custom_fields' => ['array'],
            'copies' => ['array'],
            'copies.*.id' => ['nullable', 'integer'],
            'copies.*.condition_id' => ['nullable', 'integer'],
            'copies.*.location_id' => ['nullable', 'integer'],
            'copies.*.acquired_at' => ['nullable', 'date'],
            'copies.*.price_paid' => ['nullable', 'numeric', 'min:0'],
            'copies.*.estimated_value' => ['nullable', 'numeric', 'min:0'],
            'cover' => ['nullable', 'image', 'max:10240'],
        ]);
    }

    /**
     * Turn the validated copy rows into the shape the action expects, converting
     * the price fields from currency units into integer cents.
     *
     * @param  array<int, array<string, mixed>>  $copies
     * @return list<array{id: int|null, condition_id: int|null, location_id: int|null, acquired_at: string|null, price_paid: int|null, estimated_value: int|null}>
     */
    private function copies(array $copies): array
    {
        return collect($copies)
            ->map(fn (array $copy): array => [
                'id' => $this->toId($copy['id'] ?? null),
                'condition_id' => $this->toId($copy['condition_id'] ?? null),
                'location_id' => $this->toId($copy['location_id'] ?? null),
                'acquired_at' => $copy['acquired_at'] ?? null,
                'price_paid' => $this->toCents($copy['price_paid'] ?? null),
                'estimated_value' => $this->toCents($copy['estimated_value'] ?? null),
            ])
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

    private function toCents(mixed $amount): ?int
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        return (int) round((float) $amount * 100);
    }
}
