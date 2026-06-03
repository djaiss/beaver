<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateOrganization;
use App\Actions\CreateVault;
use App\Actions\DestroyVault;
use App\Actions\UpdateOrganization;
use App\Actions\UpdateVault;
use App\Http\Controllers\Controller;
use App\Http\Resources\VaultResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class VaultController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vaults = $request->user()
            ->vaults()
            ->orderBy('id')
            ->get();

        return VaultResource::collection($vaults);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $vault = new CreateVault(
            user: $request->user(),
            name: $validated['name'],
        )->execute();

        return new VaultResource($vault)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');

        return new VaultResource($vault)
            ->response()
            ->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new UpdateVault(
            user: $request->user(),
            vault: $vault,
            name: $validated['name'],
        )->execute();

        return new VaultResource($vault->fresh())
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $vault = $request->attributes->get('vault');

        new DestroyVault(
            user: $request->user(),
            vault: $vault,
        )->execute();

        return response()->noContent(204);
    }
}
