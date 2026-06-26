<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vault\Adminland;

use App\Actions\CreateGender;
use App\Actions\DestroyGender;
use App\Actions\UpdateGender;
use App\Http\Controllers\Controller;
use App\Http\Resources\GenderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AdminlandGenderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vault = $request->attributes->get('vault');

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $genders = $vault
            ->genders()
            ->orderBy('position')
            ->paginate($perPage);

        return GenderResource::collection($genders);
    }

    public function show(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $genderId = $request->route()->parameter('gender');

        $gender = $vault->genders()->findOrFail($genderId);

        return new GenderResource($gender)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $gender = new CreateGender(
            user: $request->user(),
            vault: $vault,
            name: $validated['name'],
        )->execute();

        return new GenderResource($gender)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $genderId = $request->route()->parameter('gender');

        $gender = $vault->genders()->findOrFail($genderId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $gender = new UpdateGender(
            user: $request->user(),
            gender: $gender,
            name: $validated['name'],
            position: $gender->position,
        )->execute();

        return new GenderResource($gender)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $vault = $request->attributes->get('vault');
        $genderId = $request->route()->parameter('gender');

        $gender = $vault->genders()->findOrFail($genderId);

        new DestroyGender(
            user: $request->user(),
            gender: $gender,
        )->execute();

        return response()->noContent(204);
    }
}
