<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\CreateApiKey;
use App\Actions\DestroyApiKey;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function new(): View
    {
        return view('app.settings.security._api-create');
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $apiKey = new CreateApiKey(
            user: $request->user(),
            label: $validated['label'],
        )->execute();

        return to_route('profile.security.index')
            ->with('apiKey', $apiKey)
            ->with('status', trans('API key created'))
            ->with('status_description', trans('Your new API key is ready to use.'));
    }

    public function destroy(Request $request, int $apiKeyId): RedirectResponse
    {
        $apiKey = $request->user()
            ->tokens()
            ->where('id', $apiKeyId)
            ->first();

        if ($apiKey === null) {
            abort(404);
        }

        new DestroyApiKey(
            user: $request->user(),
            tokenId: $apiKey->id,
        )->execute();

        return to_route('profile.security.index')
            ->with('status', trans('API key deleted'))
            ->with('status_description', trans('The API key can no longer be used.'));
    }
}
