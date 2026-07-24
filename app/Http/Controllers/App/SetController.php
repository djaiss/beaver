<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateSet;
use App\Actions\DestroySet;
use App\Actions\UpdateSet;
use App\Http\Controllers\Controller;
use App\Models\Collection as CollectionModel;
use App\Models\Set;
use App\Traits\FindsItems;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetController extends Controller
{
    use FindsItems;

    public function index(Request $request, int $collection): View
    {
        $collectionModel = $this->findCollection($request, $collection);

        // names are encrypted, so the sort has to happen in memory
        $sets = $collectionModel->sets()->withCount('items')->get()->sortBy('name')->values();

        return view('app.sets.index', [
            'collection' => $collectionModel,
            'sets' => $sets,
            'totalCount' => $sets->count(),
            'ownedCount' => $sets->sum(fn (Set $set): int => min($set->items_count, $set->target_count ?? $set->items_count)),
            'targetCount' => $sets->sum(fn (Set $set): int => $set->target_count ?? $set->items_count),
        ]);
    }

    public function create(Request $request, int $collection): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);

        $validated = $request->validate($this->rules());

        new CreateSet(
            user: $request->user(),
            collection: $collectionModel,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            targetCount: isset($validated['target_count']) ? (int) $validated['target_count'] : null,
        )->execute();

        return to_route('sets.index', $collectionModel->id)
            ->with('status', __('Set created'))
            ->with('status_description', __('Items in this collection can now be tracked against it.'));
    }

    public function update(Request $request, int $collection, int $set): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $setModel = $this->findSet($collectionModel, $set);

        $validated = $request->validate($this->rules());

        new UpdateSet(
            user: $request->user(),
            set: $setModel,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            targetCount: isset($validated['target_count']) ? (int) $validated['target_count'] : null,
        )->execute();

        return to_route('sets.index', $collectionModel->id)
            ->with('status', __('Set updated'))
            ->with('status_description', __('Your changes to the set were saved.'));
    }

    public function destroy(Request $request, int $collection, int $set): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $setModel = $this->findSet($collectionModel, $set);

        new DestroySet(
            user: $request->user(),
            set: $setModel,
        )->execute();

        return to_route('sets.index', $collectionModel->id)
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
