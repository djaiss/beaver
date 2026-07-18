<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCollection;
use App\Enums\VisibilityEnum;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CollectionController extends Controller
{
    /** @var list<string> */
    private const array EMOJI_OPTIONS = ['📦', '📚', '💿', '🃏', '🍷', '🎮', '🧸', '🪙', '🖼️', '⌚', '👟', '📷'];

    public function index(Request $request): View
    {
        $account = $request->user()->account;

        return view('app.collections.index', [
            'collections' => $account->collections()->orderByDesc('updated_at')->get(),
        ]);
    }

    public function show(Request $request, int $collection): View
    {
        $account = $request->user()->account;

        try {
            $collectionModel = $account->collections()
                ->with(['collectionTypes', 'categories'])
                ->findOrFail($collection);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $items = $collectionModel->items()
            ->with(['mainPhoto', 'copies.condition', 'copies.location'])
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        return view('app.collections.show', [
            'collection' => $collectionModel,
            'view' => $collectionModel->viewForUser($request->user()),
            'items' => $items,
            'itemCount' => $collectionModel->items()->count(),
            'totalValue' => (int) Copy::whereIn('item_id', $collectionModel->items()->select('id'))->sum('estimated_value'),
        ]);
    }

    public function new(Request $request): View
    {
        $account = $request->user()->account;

        return view('app.collections.new', [
            'types' => $account->collectionTypes()->orderBy('name')->get(),
            'currencies' => $this->currencyOptions(),
            'defaultCurrency' => $account->currency_code,
            'emojiOptions' => self::EMOJI_OPTIONS,
            'visibilityOptions' => $this->visibilityOptions(),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $account = $request->user()->account;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'emoji' => ['nullable', 'string', Rule::in(self::EMOJI_OPTIONS)],
            'visibility' => ['required', Rule::enum(VisibilityEnum::class)],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'collection_type_ids' => ['array'],
            'collection_type_ids.*' => ['integer'],
        ]);

        new CreateCollection(
            user: $request->user(),
            account: $account,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            emoji: $validated['emoji'] ?? null,
            visibility: $validated['visibility'],
            currency: $validated['currency'] ?? null,
            collectionTypeIds: $validated['collection_type_ids'] ?? [],
        )->execute();

        return to_route('collections.index')
            ->with('status', __('Collection created'))
            ->with('status_description', __('Your new collection is ready for items.'));
    }

    /**
     * @return array<string, string>
     */
    private function currencyOptions(): array
    {
        return collect(config('currencies'))
            ->map(fn (array $currency, string $code): string => $currency['flag'].' '.$code)
            ->all();
    }

    /**
     * @return list<array{key: string, label: string, description: string}>
     */
    private function visibilityOptions(): array
    {
        return [
            ['key' => VisibilityEnum::Private->value, 'label' => __('Private'), 'description' => __('Only you can see this collection.')],
            ['key' => VisibilityEnum::Shared->value, 'label' => __('Shared'), 'description' => __('Visible to everyone in your account.')],
            ['key' => VisibilityEnum::Public->value, 'label' => __('Public'), 'description' => __('Anyone with the link can view it, read-only.')],
        ];
    }
}
