<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Collection;

use App\Actions\CreateCollection;
use App\Actions\DestroyCollection;
use App\Actions\UpdateCollection;
use App\Enums\VisibilityEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CollectionController extends Controller
{
    /** @var list<string> */
    private const array EMOJI_OPTIONS = ['📦', '📚', '💿', '🃏', '🍷', '🎮', '🧸', '🪙', '🖼️', '⌚', '👟', '📷'];

    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $collections = $account->collections()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return CollectionResource::collection($collections);
    }

    public function show(Request $request): JsonResponse
    {
        $collectionId = $request->route()->parameter('collection');

        $collection = $request->user()->account->collections()->findOrFail($collectionId);

        return new CollectionResource($collection)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'emoji' => ['nullable', 'string', Rule::in(self::EMOJI_OPTIONS)],
            'visibility' => ['required', Rule::enum(VisibilityEnum::class)],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'collection_type_ids' => ['array'],
            'collection_type_ids.*' => ['integer'],
        ]);

        $collection = new CreateCollection(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            emoji: $validated['emoji'] ?? null,
            visibility: $validated['visibility'],
            currency: $validated['currency'] ?? null,
            collectionTypeIds: $validated['collection_type_ids'] ?? [],
        )->execute();

        return new CollectionResource($collection)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $collectionId = $request->route()->parameter('collection');

        $collection = $request->user()->account->collections()->findOrFail($collectionId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'emoji' => ['nullable', 'string', Rule::in(self::EMOJI_OPTIONS)],
            'visibility' => ['required', Rule::enum(VisibilityEnum::class)],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
        ]);

        $collection = new UpdateCollection(
            user: $request->user(),
            collection: $collection,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            emoji: $validated['emoji'] ?? null,
            visibility: $validated['visibility'],
            currency: $validated['currency'] ?? null,
        )->execute();

        return new CollectionResource($collection)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $collectionId = $request->route()->parameter('collection');

        $collection = $request->user()->account->collections()->findOrFail($collectionId);

        new DestroyCollection(
            user: $request->user(),
            collection: $collection,
        )->execute();

        return response()->noContent(204);
    }
}
