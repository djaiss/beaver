<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Collection;

use App\Actions\AddItemPhoto;
use App\Actions\DestroyItemPhoto;
use App\Actions\SetMainItemPhoto;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemPhotoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemPhotoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;
        $item = $account->items()->findOrFail($itemId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $photos = $item->photos()->paginate($perPage);

        return ItemPhotoResource::collection($photos);
    }

    public function show(Request $request): JsonResponse
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;
        $item = $account->items()->findOrFail($itemId);
        $photoId = $request->route()->parameter('photo');

        $photo = $item->photos()->findOrFail($photoId);

        return new ItemPhotoResource($photo)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;
        $item = $account->items()->findOrFail($itemId);

        $request->validate([
            'file' => ['required', 'image', 'max:10240'],
        ]);

        $photo = new AddItemPhoto(
            user: $request->user(),
            item: $item,
            file: $request->file('file'),
        )->execute();

        return new ItemPhotoResource($photo)
            ->response()
            ->setStatusCode(201);
    }

    public function main(Request $request): JsonResponse
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;
        $item = $account->items()->findOrFail($itemId);
        $photoId = $request->route()->parameter('photo');

        $photo = $item->photos()->findOrFail($photoId);

        $photo = new SetMainItemPhoto(
            user: $request->user(),
            itemPhoto: $photo,
        )->execute();

        return new ItemPhotoResource($photo)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;
        $item = $account->items()->findOrFail($itemId);
        $photoId = $request->route()->parameter('photo');

        $photo = $item->photos()->findOrFail($photoId);

        new DestroyItemPhoto(
            user: $request->user(),
            itemPhoto: $photo,
        )->execute();

        return response()->noContent(204);
    }
}
