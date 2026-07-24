<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\DestroyItemPhoto;
use App\Helpers\FileSize;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ItemPhoto;
use App\Services\BlindIndex;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /**
     * An account can hold many thousands of photos, so a page holds a fixed
     * slice of them and everything that narrows the list down (searching,
     * filtering, sorting) happens in the database rather than in the browser.
     */
    private const int PHOTOS_PER_PAGE = 100;

    private const string FILTER_COVERS = 'covers';

    private const string FILTER_EXTRAS = 'extras';

    public function index(Request $request): View
    {
        $account = $request->user()->account;

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'filter' => ['nullable', 'string', 'in:all,covers,extras'],
            'sort' => ['nullable', 'string', 'in:newest,oldest,largest,smallest'],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));
        $filter = $validated['filter'] ?? 'all';
        $sort = $validated['sort'] ?? 'newest';

        $photos = $this->page($account, $search, $filter, $sort);

        return view('app.photos.index', [
            'view' => $request->user()->photos_view,
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
            'photos' => $photos,
            'rows' => $this->rows($photos),
            'stats' => $this->stats($account),
            'counts' => $this->counts($account),
        ]);
    }

    public function destroy(Request $request, int $itemPhoto): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $photo = ItemPhoto::query()->ofAccount($account)->findOrFail($itemPhoto);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyItemPhoto(
            user: $request->user(),
            itemPhoto: $photo,
        )->execute();

        return to_route('settings.photos.index')
            ->with('status', __('Photo deleted'))
            ->with('status_description', __('The photo and its file were removed for good.'));
    }

    /**
     * @return LengthAwarePaginator<int, ItemPhoto>
     */
    private function page(Account $account, string $search, string $filter, string $sort): LengthAwarePaginator
    {
        $query = ItemPhoto::query()
            ->ofAccount($account)
            ->with(['item.catalog', 'item.catalogType']);

        $this->applySearch($query, $search);
        $this->applyFilter($query, $filter);
        $this->applySort($query, $sort);

        return $query->paginate(self::PHOTOS_PER_PAGE)->withQueryString();
    }

    /**
     * File names and item names are encrypted, so they cannot be matched with
     * LIKE. Each word typed is hashed the same way the index was built and has
     * to be present, which makes the search an AND across the words.
     *
     * @param  Builder<ItemPhoto>  $query
     */
    private function applySearch(Builder $query, string $search): void
    {
        if ($search === '') {
            return;
        }

        $hashes = BlindIndex::hashesForQuery($search);

        // A query made only of single letters cannot match anything indexed, so
        // it is honoured as "nothing found" rather than quietly ignored.
        if ($hashes === []) {
            $query->whereRaw('1 = 0');

            return;
        }

        foreach ($hashes as $hash) {
            $query->whereHas('searchTokens', fn (Builder $tokens): Builder => $tokens->where('token', $hash));
        }
    }

    /**
     * @param  Builder<ItemPhoto>  $query
     */
    private function applyFilter(Builder $query, string $filter): void
    {
        if ($filter === self::FILTER_COVERS) {
            $query->where('is_main', true);
        }

        if ($filter === self::FILTER_EXTRAS) {
            $query->where('is_main', false);
        }
    }

    /**
     * @param  Builder<ItemPhoto>  $query
     */
    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'largest' => $query->orderByDesc('size')->orderByDesc('id'),
            'smallest' => $query->orderBy('size')->orderBy('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    /**
     * One display row per photo on the page. Everything encrypted is decrypted
     * here, so the view and the drawer never touch a model.
     *
     * @param  LengthAwarePaginator<int, ItemPhoto>  $photos
     * @return list<array<string, mixed>>
     */
    private function rows(LengthAwarePaginator $photos): array
    {
        return $photos->getCollection()->map(function (ItemPhoto $photo): array {
            $item = $photo->item;

            return [
                'id' => $photo->id,
                'url' => $photo->url(),
                'filename' => $photo->filename,
                'dimensions' => $photo->dimensions(),
                'size' => FileSize::format($photo->size),
                'format' => $photo->format(),
                'uploadedAt' => $photo->created_at->diffForHumans(),
                'uploadedBy' => $photo->created_by_name,
                'isCover' => $photo->is_main,
                'itemId' => $item->id,
                'itemName' => $item->name,
                'itemSub' => $this->itemSub($photo),
                'itemUrl' => route('items.show', [$item->catalog, $item]),
                'coverUrl' => route('settings.photos.cover.update', $photo->id),
                'deleteUrl' => route('settings.photos.destroy', $photo->id),
            ];
        })->values()->all();
    }

    /**
     * The line under an item's name: the collection it sits in, and its type
     * when it has one.
     */
    private function itemSub(ItemPhoto $photo): string
    {
        $type = $photo->item->catalogType?->name;

        if ($type === null) {
            return $photo->item->catalog->name;
        }

        return $photo->item->catalog->name.' · '.$type;
    }

    /**
     * The four figures at the top of the screen, counted over the whole account
     * rather than over the page being shown.
     *
     * @return array<string, string|int>
     */
    private function stats(Account $account): array
    {
        $base = fn (): Builder => ItemPhoto::query()->ofAccount($account);

        return [
            'total' => $base()->count(),
            'storage' => FileSize::format((int) $base()->sum('size')),
            'covers' => $base()->where('is_main', true)->count(),
            'items' => $base()->distinct()->count('item_id'),
        ];
    }

    /**
     * The counts on the filter chips. They describe the account, so they do not
     * move as the user filters.
     *
     * @return array<string, int>
     */
    private function counts(Account $account): array
    {
        $covers = ItemPhoto::query()->ofAccount($account)->where('is_main', true)->count();
        $total = ItemPhoto::query()->ofAccount($account)->count();

        return [
            'all' => $total,
            'covers' => $covers,
            'extras' => $total - $covers,
        ];
    }
}
