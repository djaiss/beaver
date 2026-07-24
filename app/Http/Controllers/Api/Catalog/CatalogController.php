<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog;

use App\Actions\CreateCatalog;
use App\Actions\DestroyCatalog;
use App\Actions\UpdateCatalog;
use App\Enums\VisibilityEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatalogResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CatalogController extends Controller
{
    /** @var list<string> */
    private const array EMOJI_OPTIONS = ['📦', '📚', '💿', '🃏', '🍷', '🎮', '🧸', '🪙', '🖼️', '⌚', '👟', '📷'];

    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $catalogs = $account->catalogs()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return CatalogResource::collection($catalogs);
    }

    public function show(Request $request): JsonResponse
    {
        $catalogId = $request->route()->parameter('collection');

        $catalog = $request->user()->account->catalogs()->findOrFail($catalogId);

        return new CatalogResource($catalog)
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

        $catalog = new CreateCatalog(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            emoji: $validated['emoji'] ?? null,
            visibility: $validated['visibility'],
            currency: $validated['currency'] ?? null,
            catalogTypeIds: $validated['collection_type_ids'] ?? [],
        )->execute();

        return new CatalogResource($catalog)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $catalogId = $request->route()->parameter('collection');

        $catalog = $request->user()->account->catalogs()->findOrFail($catalogId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'emoji' => ['nullable', 'string', Rule::in(self::EMOJI_OPTIONS)],
            'visibility' => ['required', Rule::enum(VisibilityEnum::class)],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
        ]);

        $catalog = new UpdateCatalog(
            user: $request->user(),
            catalog: $catalog,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            emoji: $validated['emoji'] ?? null,
            visibility: $validated['visibility'],
            currency: $validated['currency'] ?? null,
        )->execute();

        return new CatalogResource($catalog)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $catalogId = $request->route()->parameter('collection');

        $catalog = $request->user()->account->catalogs()->findOrFail($catalogId);

        new DestroyCatalog(
            user: $request->user(),
            catalog: $catalog,
        )->execute();

        return response()->noContent(204);
    }
}
