<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\AttachTagToItem;
use App\Actions\DetachTagFromItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemTagController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $item = $this->findItem($request);

        return TagResource::collection($item->tags()->orderBy('id')->get());
    }

    public function create(Request $request): JsonResponse
    {
        $item = $this->findItem($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $tag = new AttachTagToItem(
            user: $request->user(),
            item: $item,
            name: $validated['name'],
        )->execute();

        return new TagResource($tag)
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Request $request): Response
    {
        $item = $this->findItem($request);
        $tagId = $request->route()->parameter('tag');

        $tag = $request->user()->account->tags()->findOrFail($tagId);

        new DetachTagFromItem(
            user: $request->user(),
            item: $item,
            tag: $tag,
        )->execute();

        return response()->noContent(204);
    }

    private function findItem(Request $request): Item
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;

        return Item::query()
            ->whereHas('collection', fn ($query) => $query->whereBelongsTo($account))
            ->findOrFail($itemId);
    }
}
