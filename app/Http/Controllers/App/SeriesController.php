<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateSeries;
use App\Actions\DestroySeries;
use App\Actions\UpdateSeries;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Series;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\View\View;

class SeriesController extends Controller
{
    public function index(Request $request): View
    {
        $account = $request->user()->account;

        // The card shows which collections a series reaches into, so the items come along
        // with theirs. Names are encrypted, so both the sort and the grouping happen in memory.
        $series = $account->series()
            ->with(['items.catalog'])
            ->get()
            ->sortBy(fn (Series $one): string => mb_strtolower($one->name))
            ->values();

        return view('app.series.index', [
            'series' => $series,
            'totalCount' => $series->count(),
            'linkedItemCount' => $series->sum(fn (Series $one): int => $one->items->count()),
        ]);
    }

    public function show(Request $request, int $series): View
    {
        $seriesModel = $this->findSeries($request, $series, ['items.catalog', 'items.catalogType']);

        $groups = $this->groupItemsByCatalog($seriesModel);

        return view('app.series.show', [
            'series' => $seriesModel,
            'groups' => $groups,
            'itemCount' => $seriesModel->items->count(),
            'catalogCount' => $groups->count(),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        new CreateSeries(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            description: $validated['description'] ?? null,
        )->execute();

        return to_route('series.index')
            ->with('status', __('Series created'))
            ->with('status_description', __('Items from any collection can now be linked to it.'));
    }

    public function update(Request $request, int $series): RedirectResponse
    {
        $seriesModel = $this->findSeries($request, $series);

        $validated = $request->validate($this->rules());

        new UpdateSeries(
            user: $request->user(),
            series: $seriesModel,
            name: $validated['name'],
            description: $validated['description'] ?? null,
        )->execute();

        return to_route('series.index')
            ->with('status', __('Series updated'))
            ->with('status_description', __('Your changes to the series were saved.'));
    }

    public function destroy(Request $request, int $series): RedirectResponse
    {
        $seriesModel = $this->findSeries($request, $series);

        new DestroySeries(
            user: $request->user(),
            series: $seriesModel,
        )->execute();

        return to_route('series.index')
            ->with('status', __('Series deleted'))
            ->with('status_description', __('The items that were part of it keep their data.'));
    }

    /**
     * The items of a series, bucketed under the collection they live in. A series spans
     * collections, so this is the shape every screen wants it in.
     *
     * A bucket only exists because an item landed in it, so it is never empty.
     *
     * @return SupportCollection<int, array{catalog: Catalog, items: non-empty-list<Item>}>
     */
    private function groupItemsByCatalog(Series $series): SupportCollection
    {
        $groups = [];

        // Item and collection names are both encrypted, so neither order can come from the
        // database. Items are sorted on the way in, collections once the buckets are built.
        foreach ($series->items->sortBy(fn (Item $item): string => mb_strtolower($item->name)) as $item) {
            $catalog = $item->catalog;

            // An item always has a collection, but a series that outlives one has nowhere to
            // file its items, so they are left out rather than crashing the page.
            if (! $catalog instanceof Catalog) {
                continue;
            }

            if (! isset($groups[$item->catalog_id])) {
                $groups[$item->catalog_id] = ['catalog' => $catalog, 'items' => []];
            }

            $groups[$item->catalog_id]['items'][] = $item;
        }

        uasort($groups, fn (array $a, array $b): int => mb_strtolower($a['catalog']->name) <=> mb_strtolower($b['catalog']->name));

        return new SupportCollection(array_values($groups));
    }

    /**
     * @return array<string, list<string>>
     */
    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @param  list<string>  $with
     */
    private function findSeries(Request $request, int $series, array $with = []): Series
    {
        try {
            return $request->user()->account->series()->with($with)->findOrFail($series);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
