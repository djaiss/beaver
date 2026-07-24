<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Collection;

use App\Actions\AttachTagToItem;
use App\Actions\DetachTagFromItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemTagController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;
        $item = $account->items()->findOrFail($itemId);

        return TagResource::collection($item->tags()->orderBy('id')->get());
    }

    public function create(Request $request): JsonResponse
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;
        $item = $account->items()->findOrFail($itemId);

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
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;
        $item = $account->items()->findOrFail($itemId);
        $tagId = $request->route()->parameter('tag');

        $tag = $account->tags()->findOrFail($tagId);

        new DetachTagFromItem(
            user: $request->user(),
            item: $item,
            tag: $tag,
        )->execute();

        return response()->noContent(204);
    }
}
