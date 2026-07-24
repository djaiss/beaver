<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Collection;

use App\Actions\CreateTag;
use App\Actions\DestroyTag;
use App\Actions\UpdateTag;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $tags = $account->tags()
            ->orderBy('id')
            ->paginate($perPage);

        return TagResource::collection($tags);
    }

    public function show(Request $request): JsonResponse
    {
        $tagId = $request->route()->parameter('tag');

        $tag = $request->user()->account->tags()->findOrFail($tagId);

        return new TagResource($tag)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $tag = new CreateTag(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
        )->execute();

        return new TagResource($tag)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $tagId = $request->route()->parameter('tag');

        $tag = $request->user()->account->tags()->findOrFail($tagId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $tag = new UpdateTag(
            user: $request->user(),
            tag: $tag,
            name: $validated['name'],
        )->execute();

        return new TagResource($tag)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $tagId = $request->route()->parameter('tag');

        $tag = $request->user()->account->tags()->findOrFail($tagId);

        new DestroyTag(
            user: $request->user(),
            tag: $tag,
        )->execute();

        return response()->noContent(204);
    }
}
