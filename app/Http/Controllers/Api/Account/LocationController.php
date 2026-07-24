<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account;

use App\Actions\CreateLocation;
use App\Actions\DestroyLocation;
use App\Actions\UpdateLocation;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    /** @var list<string> */
    private const array EMOJI_OPTIONS = ['📦', '🏠', '🚪', '🛋️', '🗄️', '📚', '🧰', '🏢', '🚗', '🗃️', '🖼️', '🎁'];

    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $locations = $account->locations()
            ->orderBy('id')
            ->paginate($perPage);

        return LocationResource::collection($locations);
    }

    public function show(Request $request): JsonResponse
    {
        $locationId = $request->route()->parameter('location');

        $location = $request->user()->account->locations()->findOrFail($locationId);

        return new LocationResource($location)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'emoji' => ['nullable', 'string', Rule::in(self::EMOJI_OPTIONS)],
        ]);

        $location = new CreateLocation(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
            emoji: $validated['emoji'] ?? null,
        )->execute();

        return new LocationResource($location)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $locationId = $request->route()->parameter('location');

        $location = $request->user()->account->locations()->findOrFail($locationId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'emoji' => ['nullable', 'string', Rule::in(self::EMOJI_OPTIONS)],
        ]);

        $location = new UpdateLocation(
            user: $request->user(),
            location: $location,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
            emoji: $validated['emoji'] ?? null,
        )->execute();

        return new LocationResource($location)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $locationId = $request->route()->parameter('location');

        $location = $request->user()->account->locations()->findOrFail($locationId);

        new DestroyLocation(
            user: $request->user(),
            location: $location,
        )->execute();

        return response()->noContent(204);
    }
}
