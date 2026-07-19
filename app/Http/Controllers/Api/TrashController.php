<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\EmptyTrash;
use App\Actions\RestoreFromTrash;
use App\Enums\TrashableEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrashResource;
use App\Services\Trash;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;

class TrashController extends Controller
{
    /**
     * The trash merges five soft deleting tables into one list already sorted
     * by urgency, so it is returned whole rather than paginated.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $entries = new Trash(account: $request->user()->account)->entries();

        return TrashResource::collection($entries);
    }

    public function update(Request $request): Response
    {
        $validated = $request->validate([
            'type' => ['required', new Enum(TrashableEnum::class)],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        new RestoreFromTrash(
            user: $request->user(),
            account: $request->user()->account,
            type: TrashableEnum::from($validated['type']),
            objectId: (int) $validated['id'],
        )->execute();

        return response()->noContent(204);
    }

    public function destroy(Request $request): Response
    {
        new EmptyTrash(
            user: $request->user(),
            account: $request->user()->account,
        )->execute();

        return response()->noContent(204);
    }
}
