<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\DestroyUserAvatar;
use App\Actions\UpdateUserAvatar;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AvatarController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        new UpdateUserAvatar(
            user: $request->user(),
            file: $validated['avatar'],
        )->execute();

        return to_route('profile.index')
            ->with('status', __('Changes saved'))
            ->with('status_description', __('Your avatar was updated.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        new DestroyUserAvatar(user: $request->user())->execute();

        return to_route('profile.index')
            ->with('status', __('Changes saved'))
            ->with('status_description', __('Your avatar was removed.'));
    }

    /**
     * Stream one resized version of a user's avatar. Avatars live on the
     * private disk, so they are served through here rather than a public URL,
     * and only to the members of the account the user belongs to.
     */
    public function show(Request $request, User $user, int $size): StreamedResponse
    {
        if ($user->account_id !== $request->user()->account_id) {
            abort(404);
        }

        if (! $user->hasAvatar()) {
            abort(404);
        }

        if (! in_array($size, User::avatarPixelSizes(), true)) {
            abort(404);
        }

        $disk = Storage::disk((string) config('filesystems.default'));
        $path = $user->avatarVariantPath($size);

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, headers: [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
