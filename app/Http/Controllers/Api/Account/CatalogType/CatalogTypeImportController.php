<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account\CatalogType;

use App\Actions\ImportCatalogType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatalogTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogTypeImportController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'json' => ['required', 'string', 'max:'.ImportCatalogType::MAX_LENGTH],
        ]);

        $type = new ImportCatalogType(
            user: $request->user(),
            account: $request->user()->account,
            json: $validated['json'],
        )->execute();

        return new CatalogTypeResource($type)
            ->response()
            ->setStatusCode(201);
    }
}
