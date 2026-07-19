<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\DestroyItemPhotos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemPhotoSelectionController extends Controller
{
    /**
     * Delete several photos of the account in one call. The action is all or
     * nothing: one ID that does not belong to the account deletes none of them.
     */
    public function destroy(Request $request): Response
    {
        $validated = $request->validate([
            'photo_ids' => ['required', 'array'],
            'photo_ids.*' => ['integer'],
        ]);

        new DestroyItemPhotos(
            user: $request->user(),
            account: $request->user()->account,
            photoIds: $validated['photo_ids'],
        )->execute();

        return response()->noContent(204);
    }
}
