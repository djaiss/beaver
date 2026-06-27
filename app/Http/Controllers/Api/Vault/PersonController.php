<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vault;

use App\Actions\CreatePerson;
use App\Actions\DestroyPerson;
use App\Actions\UpdatePerson;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vault = $request->attributes->get('vault');

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $persons = $vault->persons()
            ->latest()
            ->paginate($perPage);

        return PersonResource::collection($persons);
    }

    public function show(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $personId = $request->route()->parameter('person');

        $person = $vault->persons()->findOrFail($personId);

        return new PersonResource($person)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $validated = $request->validate([
            'gender_id' => ['nullable', 'integer'],
            'kids_status' => ['nullable', Rule::in(['no_kids', 'maybe_kids', 'has_kids'])],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'maiden_name' => ['nullable', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:100'],
            'prefix' => ['nullable', 'string', 'max:100'],
        ]);

        $gender = isset($validated['gender_id'])
            ? $vault->genders()->findOrFail($validated['gender_id'])
            : null;

        $person = new CreatePerson(
            user: $request->user(),
            vault: $vault,
            gender: $gender,
            firstName: $validated['first_name'],
            middleName: $validated['middle_name'] ?? null,
            lastName: $validated['last_name'] ?? null,
            nickname: $validated['nickname'] ?? null,
            maidenName: $validated['maiden_name'] ?? null,
            suffix: $validated['suffix'] ?? null,
            prefix: $validated['prefix'] ?? null,
            kidsStatus: $validated['kids_status'] ?? null,
        )->execute();

        return new PersonResource($person)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $personId = $request->route()->parameter('person');

        $person = $vault->persons()->findOrFail($personId);
        $validated = $request->validate([
            'gender_id' => ['nullable', 'integer'],
            'kids_status' => ['nullable', Rule::in(['no_kids', 'maybe_kids', 'has_kids'])],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'maiden_name' => ['nullable', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:100'],
            'prefix' => ['nullable', 'string', 'max:100'],
        ]);

        $gender = isset($validated['gender_id'])
            ? $vault->genders()->findOrFail($validated['gender_id'])
            : null;

        $person = new UpdatePerson(
            user: $request->user(),
            person: $person,
            gender: $gender,
            firstName: $validated['first_name'],
            middleName: $validated['middle_name'] ?? null,
            lastName: $validated['last_name'] ?? null,
            nickname: $validated['nickname'] ?? null,
            maidenName: $validated['maiden_name'] ?? null,
            suffix: $validated['suffix'] ?? null,
            prefix: $validated['prefix'] ?? null,
            kidsStatus: $validated['kids_status'] ?? null,
        )->execute();

        return new PersonResource($person)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $vault = $request->attributes->get('vault');
        $personId = $request->route()->parameter('person');

        $person = $vault->persons()->findOrFail($personId);

        new DestroyPerson(
            user: $request->user(),
            person: $person,
        )->execute();

        return response()->noContent(204);
    }
}
