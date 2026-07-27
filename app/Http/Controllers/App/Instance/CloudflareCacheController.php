<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Actions\PurgeCloudflareCache;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Purging the Cloudflare cache that holds the rendered marketing pages. Like the
 * rest of the instance panel this is English only.
 */
class CloudflareCacheController extends Controller
{
    public function destroy(Request $request): RedirectResponse
    {
        $purged = new PurgeCloudflareCache(
            user: $request->user(),
        )->execute();

        if (! $purged) {
            return to_route('instanceAdmin.siteOptions.index')
                ->with('status', 'Cloudflare refused the purge')
                ->with('status_description', 'Check that CLOUDFLARE_API_TOKEN and CLOUDFLARE_ZONE_ID are set, and that the token carries the Cache Purge permission for the zone.');
        }

        return to_route('instanceAdmin.siteOptions.index')
            ->with('status', 'Cloudflare cache purged successfully')
            ->with('status_description', 'The next visitor to each marketing page gets a freshly rendered one.');
    }
}
