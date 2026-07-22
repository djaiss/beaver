<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ImportCollectionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionTypeImportController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'json' => ['required', 'string', 'max:'.ImportCollectionType::MAX_LENGTH],
        ]);

        $type = new ImportCollectionType(
            user: $request->user(),
            account: $request->user()->account,
            json: $validated['json'],
        )->execute();

        return new CollectionTypeResource($type)
            ->response()
            ->setStatusCode(201);
    }
}
