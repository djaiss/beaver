<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\DestroyLocationHistory;
use App\Actions\MoveCopy;
use App\Actions\UpdateLocationHistory;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\LocationHistory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * The location history of a copy, recorded from the history tab of its item.
 *
 * Creating a record is a move: it closes the copy's open record, opens a new one
 * and updates its current location in one step. The update and destroy paths are
 * for correcting a record that was logged wrong.
 */
class LocationHistoryController extends Controller
{
    public function create(Request $request, Catalog $catalog, Item $item, Copy $copy): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        new MoveCopy(
            user: $request->user(),
            copy: $copy,
            location: $this->findLocation($copy, (int) $validated['location_id']),
            movedAt: $validated['moved_at'] ?? null,
            reason: $validated['reason'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return to_route('items.history.show', [$catalog, $item, $copy, 'locations'])
            ->with('status', __('Copy moved'))
            ->with('status_description', __('The move was added to the history of this copy.'));
    }

    public function update(Request $request, Catalog $catalog, Item $item, Copy $copy, int $locationHistory): RedirectResponse
    {
        $recordModel = $this->findRecord($copy, $locationHistory);

        $validated = $request->validate($this->rules());

        new UpdateLocationHistory(
            user: $request->user(),
            record: $recordModel,
            location: $this->findLocation($copy, (int) $validated['location_id']),
            movedAt: $validated['moved_at'],
            movedOutAt: $validated['moved_out_at'] ?? null,
            reason: $validated['reason'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return to_route('items.history.show', [$catalog, $item, $copy, 'locations'])
            ->with('status', __('Location record updated'))
            ->with('status_description', __('Your correction to the move was saved.'));
    }

    public function destroy(Request $request, Catalog $catalog, Item $item, Copy $copy, int $locationHistory): RedirectResponse
    {
        $recordModel = $this->findRecord($copy, $locationHistory);

        new DestroyLocationHistory(
            user: $request->user(),
            record: $recordModel,
        )->execute();

        return to_route('items.history.show', [$catalog, $item, $copy, 'locations'])
            ->with('status', __('Location record deleted'))
            ->with('status_description', __('The move was removed from the history of this copy.'));
    }

    private function findRecord(Copy $copy, int $locationHistory): LocationHistory
    {
        try {
            return $copy->locationHistory()->findOrFail($locationHistory);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    private function findLocation(Copy $copy, int $locationId): Location
    {
        try {
            return $copy->item->catalog->account->locations()->findOrFail($locationId);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        return [
            'location_id' => ['required', 'integer'],
            'moved_at' => ['required', 'date'],
            'moved_out_at' => ['nullable', 'date', 'after_or_equal:moved_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
