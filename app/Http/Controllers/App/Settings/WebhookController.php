<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\CreateWebhookEndpoint;
use App\Actions\DestroyWebhookEndpoint;
use App\Http\Controllers\Controller;
use App\Models\WebhookEndpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebhookController extends Controller
{
    public function index(Request $request): View
    {
        $endpoints = $request->user()
            ->webhookEndpoints()
            ->latest()
            ->get()
            ->map(fn (WebhookEndpoint $endpoint) => (object) [
                'id' => $endpoint->id,
                'label' => $endpoint->label,
                'url' => $endpoint->url,
                'secret' => $endpoint->secret,
                'is_active' => $endpoint->is_active,
            ]);

        return view('app.settings.webhooks.index', [
            'endpoints' => $endpoints,
        ]);
    }

    public function new(): View
    {
        return view('app.settings.webhooks._create');
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        new CreateWebhookEndpoint(
            user: $request->user(),
            url: $validated['url'],
            label: $validated['label'] ?? null,
        )->execute();

        return to_route('settings.webhooks.index')
            ->with('status', trans('Webhook endpoint created'));
    }

    public function destroy(Request $request, int $webhookEndpoint): RedirectResponse
    {
        $endpoint = $request->user()
            ->webhookEndpoints()
            ->findOrFail($webhookEndpoint);

        new DestroyWebhookEndpoint(
            user: $request->user(),
            webhookEndpoint: $endpoint,
        )->execute();

        return to_route('settings.webhooks.index')
            ->with('status', trans('Webhook endpoint deleted'));
    }
}
