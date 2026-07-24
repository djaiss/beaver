<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account\CatalogType;

use App\Actions\SyncCatalogTypeCatalogs;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatalogTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogTypeCatalogController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $catalogTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);

        $validated = $request->validate([
            'catalog_ids' => ['array'],
            'catalog_ids.*' => ['integer'],
        ]);

        $type = new SyncCatalogTypeCatalogs(
            user: $request->user(),
            catalogType: $type,
            catalogIds: $validated['catalog_ids'] ?? [],
        )->execute();

        return new CatalogTypeResource($type)
            ->response()
            ->setStatusCode(200);
    }
}
