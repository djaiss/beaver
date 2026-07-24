<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account;

use App\Actions\CreateItemCondition;
use App\Actions\DestroyItemCondition;
use App\Actions\UpdateItemCondition;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemConditionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemConditionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $itemConditions = $account->itemConditions()
            ->orderBy('id')
            ->paginate($perPage);

        return ItemConditionResource::collection($itemConditions);
    }

    public function show(Request $request): JsonResponse
    {
        $itemConditionId = $request->route()->parameter('itemCondition');

        $itemCondition = $request->user()->account->itemConditions()->findOrFail($itemConditionId);

        return new ItemConditionResource($itemCondition)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $itemCondition = new CreateItemCondition(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
        )->execute();

        return new ItemConditionResource($itemCondition)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $itemConditionId = $request->route()->parameter('itemCondition');

        $itemCondition = $request->user()->account->itemConditions()->findOrFail($itemConditionId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $itemCondition = new UpdateItemCondition(
            user: $request->user(),
            itemCondition: $itemCondition,
            name: $validated['name'],
        )->execute();

        return new ItemConditionResource($itemCondition)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $itemConditionId = $request->route()->parameter('itemCondition');

        $itemCondition = $request->user()->account->itemConditions()->findOrFail($itemConditionId);

        new DestroyItemCondition(
            user: $request->user(),
            itemCondition: $itemCondition,
        )->execute();

        return response()->noContent(204);
    }
}
