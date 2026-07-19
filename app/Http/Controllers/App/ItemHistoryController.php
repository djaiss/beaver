<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The history tab of an item, read one copy at a time.
 *
 * The tab is the chronological view across everything hanging off a single copy,
 * so the copy is chosen first and lives in the url: the bare tab lands on the
 * first copy, and each copy pill links to its own. Most of what the tab will
 * read from does not exist yet, so it shows the sections it will hold and fills
 * only the valuations, which are the one history the copy restructuring brought.
 */
class ItemHistoryController extends Controller
{
    use FindsItems;

    public function index(Request $request, int $collection, int $item): View
    {
        return $this->render($request, $collection, $item, null);
    }

    public function show(Request $request, int $collection, int $item, int $copy): View
    {
        return $this->render($request, $collection, $item, $copy);
    }

    private function render(Request $request, int $collection, int $item, ?int $copyId): View
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, [
            'tags',
            'copies.condition',
            'copies.currentLocation',
            'copies.latestValuation',
            'copies.valuations',
            'category',
            'collectionType',
        ]);

        // The bare tab lands on the first copy. A copy named in the url has to be
        // one of this item's own, or it is not found rather than silently
        // falling back to the first.
        $selectedCopy = $copyId === null
            ? $itemModel->copies->first()
            : $itemModel->copies->firstWhere('id', $copyId);

        if ($copyId !== null && ! $selectedCopy instanceof Copy) {
            abort(404);
        }

        return view('app.items.history', [
            'collection' => $collectionModel,
            'item' => $itemModel,
            'tags' => $this->accountTags($request),
            'selectedCopy' => $selectedCopy,
            'section' => $this->section($request),
        ]);
    }

    /**
     * Which section of the history to show. The section is a query parameter so
     * a copy's history stays one url per copy, and it falls back to the timeline
     * rather than trusting whatever the query string holds.
     */
    private function section(Request $request): string
    {
        $section = (string) $request->query('section', 'timeline');

        return in_array($section, self::SECTIONS, true) ? $section : 'timeline';
    }

    /**
     * @var list<string>
     */
    private const array SECTIONS = [
        'timeline',
        'transactions',
        'provenance',
        'valuations',
        'insurance',
        'maintenance',
        'loans',
        'locations',
        'documents',
    ];
}
