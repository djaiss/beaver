<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\CollectionType;

use App\Actions\SyncCollectionTypeCollections;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionTypeCollectionController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $collectionTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->collectionTypes()->findOrFail($collectionTypeId);

        $validated = $request->validate([
            'collection_ids' => ['array'],
            'collection_ids.*' => ['integer'],
        ]);

        $type = new SyncCollectionTypeCollections(
            user: $request->user(),
            collectionType: $type,
            collectionIds: $validated['collection_ids'] ?? [],
        )->execute();

        return new CollectionTypeResource($type)
            ->response()
            ->setStatusCode(200);
    }
}
