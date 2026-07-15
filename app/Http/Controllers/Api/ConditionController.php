<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateCondition;
use App\Actions\DestroyCondition;
use App\Actions\UpdateCondition;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConditionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ConditionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $conditions = $account->conditions()
            ->orderBy('id')
            ->paginate($perPage);

        return ConditionResource::collection($conditions);
    }

    public function show(Request $request): JsonResponse
    {
        $conditionId = $request->route()->parameter('condition');

        $condition = $request->user()->account->conditions()->findOrFail($conditionId);

        return new ConditionResource($condition)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $condition = new CreateCondition(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
        )->execute();

        return new ConditionResource($condition)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $conditionId = $request->route()->parameter('condition');

        $condition = $request->user()->account->conditions()->findOrFail($conditionId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $condition = new UpdateCondition(
            user: $request->user(),
            condition: $condition,
            name: $validated['name'],
        )->execute();

        return new ConditionResource($condition)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $conditionId = $request->route()->parameter('condition');

        $condition = $request->user()->account->conditions()->findOrFail($conditionId);

        new DestroyCondition(
            user: $request->user(),
            condition: $condition,
        )->execute();

        return response()->noContent(204);
    }
}
