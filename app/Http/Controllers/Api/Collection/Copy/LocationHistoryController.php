<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Collection\Copy;

use App\Actions\DestroyLocationHistory;
use App\Actions\MoveCopy;
use App\Actions\UpdateLocationHistory;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationHistoryResource;
use App\Models\Copy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LocationHistoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $records = $copy->locationHistory()->paginate($perPage);

        return LocationHistoryResource::collection($records);
    }

    public function show(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('locationHistory');
        $record = $copy->locationHistory()->findOrFail($recordId);

        return new LocationHistoryResource($record)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);

        $validated = $request->validate([
            'location_id' => ['required', 'integer'],
            'moved_at' => ['required', 'date'],
            'moved_out_at' => ['nullable', 'date', 'after_or_equal:moved_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        new MoveCopy(
            user: $request->user(),
            copy: $copy,
            location: $account->locations()->findOrFail((int) $validated['location_id']),
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
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('locationHistory');
        $record = $copy->locationHistory()->findOrFail($recordId);

        $validated = $request->validate([
            'location_id' => ['required', 'integer'],
            'moved_at' => ['required', 'date'],
            'moved_out_at' => ['nullable', 'date', 'after_or_equal:moved_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $record = new UpdateLocationHistory(
            user: $request->user(),
            record: $record,
            location: $account->locations()->findOrFail((int) $validated['location_id']),
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
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('locationHistory');
        $record = $copy->locationHistory()->findOrFail($recordId);

        new DestroyLocationHistory(
            user: $request->user(),
            record: $record,
        )->execute();

        return response()->noContent(204);
    }
}
