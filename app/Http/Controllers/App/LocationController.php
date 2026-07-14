<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateLocation;
use App\Actions\DestroyLocation;
use App\Actions\UpdateLocation;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $account = $request->user()->account;

        $locations = $account->locations()->get();

        return view('app.locations.index', [
            'tree' => $this->buildTree($locations),
            'parentOptions' => ['' => __('No parent')] + $locations->sortBy('name')->pluck('name', 'id')->all(),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        new CreateLocation(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
        )->execute();

        return to_route('locations.index')
            ->with('status', __('Location created'));
    }

    public function update(Request $request, int $location): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $locationModel = $account->locations()->findOrFail($location);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        new UpdateLocation(
            user: $request->user(),
            location: $locationModel,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
        )->execute();

        return to_route('locations.index')
            ->with('status', __('Location updated'));
    }

    public function destroy(Request $request, int $location): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $locationModel = $account->locations()->findOrFail($location);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyLocation(
            user: $request->user(),
            location: $locationModel,
        )->execute();

        return to_route('locations.index')
            ->with('status', __('Location deleted'));
    }

    /**
     * Group the flat list of locations into a nested tree, sorted by name
     * within each level.
     *
     * @param  Collection<int, Location>  $locations
     * @return list<array{location: Location, children: array<mixed>}>
     */
    private function buildTree(Collection $locations, ?int $parentId = null): array
    {
        return $locations
            ->where('parent_id', $parentId)
            ->sortBy('name')
            ->map(fn (Location $location): array => [
                'location' => $location,
                'children' => $this->buildTree($locations, $location->id),
            ])
            ->values()
            ->all();
    }
}
