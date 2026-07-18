<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\EmptyTrash;
use App\Actions\RestoreFromTrash;
use App\Enums\TrashableEnum;
use App\Http\Controllers\Controller;
use App\Services\Trash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class TrashController extends Controller
{
    public function index(Request $request): View
    {
        $entries = new Trash(account: $request->user()->account)->entries();

        return view('app.trash.index', [
            'entries' => $entries,
            'retentionDays' => config('trash.retention_days'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', new Enum(TrashableEnum::class)],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        try {
            new RestoreFromTrash(
                user: $request->user(),
                account: $request->user()->account,
                type: TrashableEnum::from($validated['type']),
                objectId: (int) $validated['id'],
            )->execute();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return to_route('settings.trash.index')
            ->with('status', __('Restored'))
            ->with('status_description', __('The object was moved back to where it was.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            new EmptyTrash(
                user: $request->user(),
                account: $request->user()->account,
            )->execute();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return to_route('settings.trash.index')
            ->with('status', __('Trash emptied'))
            ->with('status_description', __('Everything in the trash was permanently deleted.'));
    }
}
