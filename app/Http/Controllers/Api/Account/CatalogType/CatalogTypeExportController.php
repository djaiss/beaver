<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account\CatalogType;

use App\Actions\ExportCatalogType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatalogTypeExportResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogTypeExportController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $catalogTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);

        $schema = new ExportCatalogType(
            user: $request->user(),
            catalogType: $type,
        )->execute();

        return new CatalogTypeExportResource($type, $schema)
            ->response()
            ->setStatusCode(200);
    }
}
