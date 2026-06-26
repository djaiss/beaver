<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Administration;

use App\Actions\CreateApiKey;
use App\Actions\DestroyApiKey;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AdministrationApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $apiKeys = $request->user()->tokens;

        return ApiResource::collection($apiKeys);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
        ]);

        $token = new CreateApiKey(
            user: $request->user(),
            label: $validated['label'],
        )->execute();

        $apiKey = $request->user()->tokens()->latest()->first();

        return new ApiResource($apiKey)
            ->additional(['token' => $token])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request): JsonResponse
    {
        $id = (int) $request->route()->parameter('id');

        $apiKey = $request->user()->tokens()->findOrFail($id);

        return new ApiResource($apiKey)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response|JsonResponse
    {
        $id = (int) $request->route()->parameter('id');

        $request->user()->tokens()->findOrFail($id);

        new DestroyApiKey(
            user: $request->user(),
            tokenId: $id,
        )->execute();

        return response()->noContent(204);
    }
}
