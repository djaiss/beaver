<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateTag;
use App\Actions\DestroyTag;
use App\Actions\UpdateTag;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View
    {
        $tags = $request->user()->account->tags()
            ->get()
            ->sortBy(fn (Tag $tag): string => mb_strtolower($tag->name))
            ->values();

        return view('app.tags.index', [
            'tags' => $tags,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new CreateTag(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
        )->execute();

        return to_route('settings.tags.index')
            ->with('status', __('Tag created'))
            ->with('status_description', __('The tag can now be applied to items.'));
    }

    public function update(Request $request, int $tag): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $tagModel = $account->tags()->findOrFail($tag);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new UpdateTag(
            user: $request->user(),
            tag: $tagModel,
            name: $validated['name'],
        )->execute();

        return to_route('settings.tags.index')
            ->with('status', __('Tag updated'))
            ->with('status_description', __('Your changes to the tag were saved.'));
    }

    public function destroy(Request $request, int $tag): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $tagModel = $account->tags()->findOrFail($tag);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyTag(
            user: $request->user(),
            tag: $tagModel,
        )->execute();

        return to_route('settings.tags.index')
            ->with('status', __('Tag deleted'))
            ->with('status_description', __('The tag was removed from the account.'));
    }
}
