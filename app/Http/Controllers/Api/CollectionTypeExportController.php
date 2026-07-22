<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ExportCollectionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionTypeExportResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionTypeExportController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $collectionTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->collectionTypes()->findOrFail($collectionTypeId);

        $schema = new ExportCollectionType(
            user: $request->user(),
            collectionType: $type,
        )->execute();

        return new CollectionTypeExportResource($type, $schema)
            ->response()
            ->setStatusCode(200);
    }
}
