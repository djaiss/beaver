<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\SetMainItemPhoto;
use App\Http\Controllers\Controller;
use App\Models\ItemPhoto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PhotoCoverController extends Controller
{
    public function update(Request $request, int $itemPhoto): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $photo = ItemPhoto::query()->ofAccount($account)->findOrFail($itemPhoto);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new SetMainItemPhoto(
            user: $request->user(),
            itemPhoto: $photo,
        )->execute();

        return to_route('settings.photos.index')
            ->with('status', __('Cover photo set'))
            ->with('status_description', __('This photo is now the visual its item is shown with.'));
    }
}
