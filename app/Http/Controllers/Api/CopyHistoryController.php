<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\TimelineSource;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimelineEntryResource;
use App\Models\Copy;
use App\Services\BuildCopyHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * The unified history of a copy: every record on it, merged into one
 * chronological read, newest first. Nothing is stored; the timeline is assembled
 * from the source models at read time and each stays the source of truth for its
 * own data.
 *
 * The meaningful entries read by default. The complete view adds the routine
 * ones (ordinary moves, informal loans, routine maintenance), and a type filter
 * narrows the result to chosen sources.
 */
class CopyHistoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;

        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)
            ->findOrFail($copyId);

        $meaningfulOnly = $request->query('view') !== 'complete';

        $entries = new BuildCopyHistory($copy)->entries($meaningfulOnly, $this->selectedTypes($request));

        return TimelineEntryResource::collection($entries);
    }

    /**
     * The event types the timeline is filtered to, kept to the real sources.
     *
     * @return list<string>
     */
    private function selectedTypes(Request $request): array
    {
        return array_values(array_filter(
            (array) $request->query('type', []),
            fn (mixed $type): bool => is_string($type) && TimelineSource::tryFrom($type) !== null,
        ));
    }
}
