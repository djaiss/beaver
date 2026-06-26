<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\CreateApiKey;
use App\Actions\DestroyApiKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\StoreApiKeyRequest;
use App\ViewModels\Settings\ApiKeyCreateViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function create(): View
    {
        $view = new ApiKeyCreateViewModel;

        return view('app.settings.security._api-create', [
            'view' => $view,
        ]);
    }

    public function store(StoreApiKeyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $apiKey = new CreateApiKey(
            user: $request->user(),
            label: $validated['label'],
        )->execute();

        return to_route('settings.security.index')
            ->with('apiKey', $apiKey)
            ->with('status', trans('API key created'));
    }

    public function destroy(Request $request, int $apiKeyId): RedirectResponse
    {
        $apiKey = $request
            ->user()
            ->tokens()
            ->where('id', $apiKeyId)
            ->first();

        new DestroyApiKey(
            user: $request->user(),
            tokenId: $apiKey->id,
        )->execute();

        return to_route('settings.security.index')
            ->with('status', trans('API key deleted'));
    }
}
