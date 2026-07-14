<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\SyncCollectionTypeCollections;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CollectionTypeCollectionController extends Controller
{
    public function update(Request $request, int $collectionType): RedirectResponse
    {
        try {
            $type = $request->user()->account->collectionTypes()->findOrFail($collectionType);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'collection_ids' => ['array'],
            'collection_ids.*' => ['integer'],
        ]);

        new SyncCollectionTypeCollections(
            user: $request->user(),
            collectionType: $type,
            collectionIds: $validated['collection_ids'] ?? [],
        )->execute();

        return to_route('types.edit', $type->id)
            ->with('status', __('Collections updated'));
    }
}
