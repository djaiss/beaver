<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\UpdateCollectionItemView;
use App\Enums\ItemViewEnum;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CollectionItemViewController extends Controller
{
    public function update(Request $request, int $collection): Response
    {
        $account = $request->user()->account;

        try {
            $collectionModel = $account->collections()->findOrFail($collection);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'view' => ['required', Rule::in(ItemViewEnum::values())],
        ]);

        new UpdateCollectionItemView(
            user: $request->user(),
            collection: $collectionModel,
            view: $validated['view'],
        )->execute();

        return response()->noContent();
    }
}
