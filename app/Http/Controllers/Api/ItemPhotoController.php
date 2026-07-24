<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\AddItemPhoto;
use App\Actions\DestroyItemPhoto;
use App\Actions\SetMainItemPhoto;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemPhotoResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemPhotoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $item = $this->findItem($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $photos = $item->photos()->paginate($perPage);

        return ItemPhotoResource::collection($photos);
    }

    public function show(Request $request): JsonResponse
    {
        $item = $this->findItem($request);
        $photoId = $request->route()->parameter('photo');

        $photo = $item->photos()->findOrFail($photoId);

        return new ItemPhotoResource($photo)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $item = $this->findItem($request);

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
        $item = $this->findItem($request);
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
        $item = $this->findItem($request);
        $photoId = $request->route()->parameter('photo');

        $photo = $item->photos()->findOrFail($photoId);

        new DestroyItemPhoto(
            user: $request->user(),
            itemPhoto: $photo,
        )->execute();

        return response()->noContent(204);
    }

    private function findItem(Request $request): Item
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;

        return $account->items()->findOrFail($itemId);
    }
}
