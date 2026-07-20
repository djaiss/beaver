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
 * the three that are built: the valuations, the transactions and the provenance,
 * each in its own panel.
 */
class ItemHistoryController extends Controller
{
    use FindsItems;

    public function index(Request $request, int $collection, int $item): View
    {
        return $this->render($request, $collection, $item, null, null);
    }

    public function show(Request $request, int $collection, int $item, int $copy, ?string $section = null): View
    {
        return $this->render($request, $collection, $item, $copy, $section);
    }

    private function render(Request $request, int $collection, int $item, ?int $copyId, ?string $section): View
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, [
            'tags',
            'copies.condition',
            'copies.currentLocation',
            'copies.latestValuation',
            'copies.valuations',
            'copies.transactions.provenanceEvent',
            'copies.provenanceEvents.transaction',
            'copies.insuranceRecords',
            'copies.maintenanceRecords.conditionBefore',
            'copies.maintenanceRecords.conditionAfter',
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
            'section' => $this->section($section),
            'currencies' => $this->currencyOptions(),
            'conditions' => $this->conditionOptions($request),
        ]);
    }

    /**
     * The conditions a maintenance record can name for the copy before and after
     * the work, drawn from the account's own conditions.
     *
     * @return array<int, string>
     */
    private function conditionOptions(Request $request): array
    {
        return $request->user()->account->conditions()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($condition): array => [$condition->id => $condition->name])
            ->all();
    }

    /**
     * Which section of the history to show. Each section is its own url, and it
     * falls back to the timeline rather than trusting whatever the url holds.
     */
    private function section(?string $section): string
    {
        return in_array($section, self::SECTIONS, true) ? $section : 'timeline';
    }

    /**
     * The currencies a transaction can be recorded in.
     *
     * @return array<string, string>
     */
    private function currencyOptions(): array
    {
        return collect(config('currencies'))
            ->map(fn (array $currency, string $code): string => $currency['flag'].' '.$code)
            ->all();
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
