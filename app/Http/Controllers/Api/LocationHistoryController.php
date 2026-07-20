<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\DestroyLocationHistory;
use App\Actions\MoveCopy;
use App\Actions\UpdateLocationHistory;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationHistoryResource;
use App\Models\Copy;
use App\Models\Location;
use App\Models\LocationHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LocationHistoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copy = $this->findCopy($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $records = $copy->locationHistory()->paginate($perPage);

        return LocationHistoryResource::collection($records);
    }

    public function show(Request $request): JsonResponse
    {
        $record = $this->findRecord($request);

        return new LocationHistoryResource($record)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copy = $this->findCopy($request);

        $validated = $this->validatePayload($request);

        new MoveCopy(
            user: $request->user(),
            copy: $copy,
            location: $this->findLocation($copy, (int) $validated['location_id']),
            movedAt: $validated['moved_at'] ?? null,
            reason: $validated['reason'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        $record = $copy->openLocationHistory()->first();

        return new LocationHistoryResource($record)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $record = $this->findRecord($request);

        $validated = $this->validatePayload($request);

        $record = new UpdateLocationHistory(
            user: $request->user(),
            record: $record,
            location: $this->findLocation($record->copy, (int) $validated['location_id']),
            movedAt: $validated['moved_at'],
            movedOutAt: $validated['moved_out_at'] ?? null,
            reason: $validated['reason'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return new LocationHistoryResource($record)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $record = $this->findRecord($request);

        new DestroyLocationHistory(
            user: $request->user(),
            record: $record,
        )->execute();

        return response()->noContent(204);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'location_id' => ['required', 'integer'],
            'moved_at' => ['required', 'date'],
            'moved_out_at' => ['nullable', 'date', 'after_or_equal:moved_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);
    }

    private function findCopy(Request $request): Copy
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;

        return Copy::query()
            ->whereHas('item.collection', fn ($query) => $query->whereBelongsTo($account))
            ->findOrFail($copyId);
    }

    private function findRecord(Request $request): LocationHistory
    {
        $copy = $this->findCopy($request);
        $recordId = $request->route()->parameter('locationHistory');

        return $copy->locationHistory()->findOrFail($recordId);
    }

    private function findLocation(Copy $copy, int $locationId): Location
    {
        return $copy->item->collection->account->locations()->findOrFail($locationId);
    }
}
