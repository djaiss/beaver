<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateSet;
use App\Actions\DestroySet;
use App\Actions\UpdateSet;
use App\Http\Controllers\Controller;
use App\Models\Collection as CollectionModel;
use App\Models\Set;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetController extends Controller
{
    public function index(Request $request): View
    {
        $collection = $request->attributes->get('collection');

        // names are encrypted, so the sort has to happen in memory
        $sets = $collection->sets()->withCount('items')->get()->sortBy('name')->values();

        return view('app.sets.index', [
            'sets' => $sets,
            'totalCount' => $sets->count(),
            'ownedCount' => $sets->sum(fn (Set $set): int => min($set->items_count, $set->target_count ?? $set->items_count)),
            'targetCount' => $sets->sum(fn (Set $set): int => $set->target_count ?? $set->items_count),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $collection = $request->attributes->get('collection');

        $validated = $request->validate($this->rules());

        new CreateSet(
            user: $request->user(),
            collection: $collection,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            targetCount: isset($validated['target_count']) ? (int) $validated['target_count'] : null,
        )->execute();

        return to_route('sets.index', $collection->id)
            ->with('status', __('Set created'))
            ->with('status_description', __('Items in this collection can now be tracked against it.'));
    }

    public function update(Request $request, CollectionModel $collection, int $set): RedirectResponse
    {
        $setModel = $this->findSet($collection, $set);

        $validated = $request->validate($this->rules());

        new UpdateSet(
            user: $request->user(),
            set: $setModel,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            targetCount: isset($validated['target_count']) ? (int) $validated['target_count'] : null,
        )->execute();

        return to_route('sets.index', $collection->id)
            ->with('status', __('Set updated'))
            ->with('status_description', __('Your changes to the set were saved.'));
    }

    public function destroy(Request $request, CollectionModel $collection, int $set): RedirectResponse
    {
        new DestroySet(
            user: $request->user(),
            set: $this->findSet($collection, $set),
        )->execute();

        return to_route('sets.index', $collection->id)
            ->with('status', __('Set deleted'))
            ->with('status_description', __('The items that were part of it keep their data.'));
    }

    /**
     * @return array<string, list<string>>
     */
    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ];
    }

    private function findSet(CollectionModel $collection, int $set): Set
    {
        try {
            return $collection->sets()->findOrFail($set);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
