<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemLogResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * The activity trail of an item. It is read only: entries are written by the
 * app as actions are performed, never by an API client.
 */
class ItemLogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $item = $this->findItem($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $logs = $item->logs()
            ->with('user')
            ->latest()
            ->paginate($perPage);

        return ItemLogResource::collection($logs);
    }

    public function show(Request $request): JsonResponse
    {
        $item = $this->findItem($request);
        $logId = $request->route()->parameter('log');

        $log = $item->logs()->with('user')->findOrFail($logId);

        return new ItemLogResource($log)
            ->response()
            ->setStatusCode(200);
    }

    private function findItem(Request $request): Item
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;

        return $account->items()->findOrFail($itemId);
    }
}
