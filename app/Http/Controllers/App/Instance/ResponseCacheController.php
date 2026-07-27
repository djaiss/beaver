<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Actions\ClearResponseCache;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Clearing the response cache that holds the rendered marketing pages. Like the
 * rest of the instance panel this is English only.
 */
class ResponseCacheController extends Controller
{
    public function destroy(Request $request): RedirectResponse
    {
        new ClearResponseCache(
            user: $request->user(),
        )->execute();

        return to_route('instanceAdmin.siteOptions.index')
            ->with('status', 'Response cache cleared successfully')
            ->with('status_description', 'The next visitor to each marketing page gets a freshly rendered one.');
    }
}
