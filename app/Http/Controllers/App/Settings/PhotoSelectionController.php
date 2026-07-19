<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\DestroyItemPhotos;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Deleting the selection made on the photos screen. It lives apart from
 * PhotoController so both keep a plain resource action name.
 */
class PhotoSelectionController extends Controller
{
    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        try {
            $deleted = new DestroyItemPhotos(
                user: $request->user(),
                account: $request->user()->account,
                photoIds: array_map('intval', $validated['ids']),
            )->execute();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return to_route('settings.photos.index')
            ->with('status', trans_choice(':count photo deleted|:count photos deleted', $deleted, ['count' => $deleted]))
            ->with('status_description', __('The photos and their files were removed for good.'));
    }
}
