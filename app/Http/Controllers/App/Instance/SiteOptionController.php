<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Actions\UpdateSiteOptions;
use App\Http\Controllers\Controller;
use App\Models\SiteOption;
use App\Services\CloudflareCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The instance wide options: for now the announcement banner shown at the top of
 * the marketing site. Like the rest of the instance panel this is English only,
 * so its copy is written as plain strings rather than through __().
 *
 * The banner sentence and link label are per locale, since the marketing site is
 * served in every supported language.
 */
class SiteOptionController extends Controller
{
    public function index(): View
    {
        return view('app.instance.siteOptions.index', [
            'siteOption' => SiteOption::current(),
            'locales' => $this->locales(),
            // An instance that does not sit behind Cloudflare has nothing to purge,
            // so the panel does not offer it.
            'cloudflareConfigured' => CloudflareCache::isConfigured(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'banner_enabled' => ['required', 'boolean'],
            'banner_version' => ['nullable', 'string', 'max:20'],
            'banner_url' => ['nullable', 'url:http,https', 'max:255'],
        ];

        foreach ($this->locales() as $locale) {
            $rules["banner_content.{$locale}.text"] = ['nullable', 'string', 'max:255'];
            $rules["banner_content.{$locale}.link_label"] = ['nullable', 'string', 'max:60'];
        }

        $validated = $request->validate($rules);

        new UpdateSiteOptions(
            user: $request->user(),
            bannerEnabled: (bool) $validated['banner_enabled'],
            bannerVersion: $validated['banner_version'] ?? null,
            bannerUrl: $validated['banner_url'] ?? null,
            bannerContent: $validated['banner_content'] ?? [],
        )->execute();

        return to_route('instanceAdmin.siteOptions.index')
            ->with('status', 'Site options updated successfully')
            ->with('status_description', 'The Cloudflare cache was purged, so the change is live.');
    }

    /**
     * The locales the banner can be written in, which are the ones the marketing
     * site is served in.
     *
     * @return list<string>
     */
    private function locales(): array
    {
        return config('app.supported_locales');
    }
}
