<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateItem;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function show(Request $request, int $collection, int $item): View
    {
        $collectionModel = $this->findCollection($request, $collection);

        try {
            $itemModel = $collectionModel->items()
                ->with([
                    'photos',
                    'copies.condition',
                    'copies.location',
                    'tags',
                    'category',
                    'set',
                    'collectionType.customFieldGroups' => fn ($query) => $query->orderBy('position')->orderBy('id'),
                    'collectionType.customFieldGroups.customFields' => fn ($query) => $query->orderBy('position')->orderBy('id'),
                    'collectionType.ungroupedCustomFields' => fn ($query) => $query->orderBy('position')->orderBy('id'),
                    'customFieldValues',
                ])
                ->findOrFail($item);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('app.items.show', [
            'collection' => $collectionModel,
            'item' => $itemModel,
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
            'tags' => $account->tags()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request, int $collection): RedirectResponse
    {
        $account = $request->user()->account;

        $collectionModel = $this->findCollection($request, $collection);

        $validated = $request->validate([
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
            'copies.*.condition_id' => ['nullable', 'integer'],
            'copies.*.location_id' => ['nullable', 'integer'],
            'copies.*.acquired_at' => ['nullable', 'date'],
            'copies.*.price_paid' => ['nullable', 'numeric', 'min:0'],
            'copies.*.estimated_value' => ['nullable', 'numeric', 'min:0'],
            'cover' => ['nullable', 'image', 'max:10240'],
        ]);

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

    private function findCollection(Request $request, int $collection): Collection
    {
        try {
            return $request->user()->account->collections()->findOrFail($collection);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * Turn the validated copy rows into the shape the action expects, converting
     * the price fields from currency units into integer cents.
     *
     * @param  array<int, array<string, mixed>>  $copies
     * @return list<array{condition_id: int|null, location_id: int|null, acquired_at: string|null, price_paid: int|null, estimated_value: int|null}>
     */
    private function copies(array $copies): array
    {
        return collect($copies)
            ->map(fn (array $copy): array => [
                'condition_id' => $copy['condition_id'] ?? null,
                'location_id' => $copy['location_id'] ?? null,
                'acquired_at' => $copy['acquired_at'] ?? null,
                'price_paid' => $this->toCents($copy['price_paid'] ?? null),
                'estimated_value' => $this->toCents($copy['estimated_value'] ?? null),
            ])
            ->all();
    }

    private function toCents(mixed $amount): ?int
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        return (int) round((float) $amount * 100);
    }
}
